@extends('layouts.admin')

@section('content')
<div class="mb-6 flex items-start justify-between flex-wrap gap-4">
    <div>
        <h2 class="text-3xl font-black text-mainText tracking-tight">Edit Email Template</h2>
        <p class="text-sm text-mutedText font-medium mt-1">
            Editing: <span class="text-primary font-mono font-bold">{{ $template->name }}</span>
        </p>
    </div>
    <a href="{{ route('admin.email-templates.index') }}"
       class="flex items-center gap-2 text-sm font-bold text-mutedText hover:text-primary bg-navy border border-primary/10 hover:border-primary/30 px-5 py-2.5 rounded-xl transition-all">
        <i class="fas fa-arrow-left"></i> Back to Templates
    </a>
</div>

@if(session('success'))
    <div class="mb-6 flex items-center gap-3 bg-green-500/10 border border-green-500/30 text-green-400 px-5 py-3.5 rounded-xl text-sm font-bold">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
@endif

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

    {{-- Editor --}}
    <div class="xl:col-span-2 space-y-5">
        <div class="bg-surface rounded-2xl border border-primary/10 shadow-sm p-6 md:p-8 relative overflow-hidden">
            <div class="absolute -top-20 -right-20 w-60 h-60 bg-primary/5 blur-[80px] rounded-full pointer-events-none"></div>

            <form action="{{ route('admin.email-templates.update', $template->key) }}" method="POST" class="space-y-6 relative z-10">
                @csrf
                @method('PUT')

                {{-- Subject --}}
                <div>
                    <label class="block text-xs font-bold text-mutedText uppercase tracking-widest mb-2">Email Subject <span class="text-red-500">*</span></label>
                    <input type="text" name="subject" value="{{ old('subject', $template->subject) }}" required
                           class="w-full bg-navy border border-primary/10 rounded-xl px-4 py-3 text-sm font-bold text-mainText focus:border-primary outline-none focus:ring-1 focus:ring-primary transition-all shadow-sm">
                    <p class="text-[11px] text-mutedText mt-1.5">
                        <i class="fas fa-info-circle text-primary/70 mr-1"></i>
                        Use <code class="bg-primary/10 text-primary px-1 rounded">@{{variable_name}}</code> syntax to insert dynamic values.
                    </p>
                    @error('subject') <p class="text-red-500 text-xs font-bold mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Body Editor --}}
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-xs font-bold text-mutedText uppercase tracking-widest">Email Body (HTML) <span class="text-red-500">*</span></label>
                        <div class="flex items-center gap-2">
                            <button type="button" onclick="setMode('visual')" id="btn-visual"
                                    class="text-[11px] font-bold px-3 py-1.5 rounded-lg bg-primary text-white transition-all">
                                <i class="fas fa-eye mr-1"></i> Visual
                            </button>
                            <button type="button" onclick="setMode('code')" id="btn-code"
                                    class="text-[11px] font-bold px-3 py-1.5 rounded-lg bg-navy border border-primary/10 text-mutedText hover:text-primary transition-all">
                                <i class="fas fa-code mr-1"></i> HTML
                            </button>
                        </div>
                    </div>

                    {{-- Visual (TinyMCE) --}}
                    <div id="visual-editor" class="rounded-xl overflow-hidden border border-primary/10">
                        <textarea id="body_editor" name="body">{{ old('body', $template->body) }}</textarea>
                    </div>

                    {{-- Raw HTML --}}
                    <div id="code-editor" class="hidden">
                        <textarea name="body" id="body_code" rows="20"
                                  class="w-full bg-[#0f172a] border border-primary/10 rounded-xl px-4 py-3 text-xs font-mono text-slate-200 focus:border-primary outline-none focus:ring-1 focus:ring-primary transition-all shadow-sm resize-y">{{ old('body', $template->body) }}</textarea>
                    </div>

                    @error('body') <p class="text-red-500 text-xs font-bold mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex justify-end gap-3 pt-6 border-t border-primary/10">
                    <a href="{{ route('admin.email-templates.index') }}"
                       class="px-6 py-3 rounded-xl font-bold text-sm text-mutedText bg-navy border border-primary/10 hover:border-primary/30 transition-all">
                        Cancel
                    </a>
                    <button type="submit" class="bg-primary text-white hover:bg-primary/90 px-8 py-3 rounded-xl font-bold uppercase tracking-widest text-sm transition-all shadow-lg flex items-center gap-3">
                        <i class="fas fa-save"></i> Save Template
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Sidebar: Variables Reference --}}
    <div class="space-y-5">
        {{-- Available Variables --}}
        <div class="bg-surface rounded-2xl border border-primary/10 shadow-sm p-5">
            <h3 class="text-sm font-black text-mainText uppercase tracking-widest mb-4 flex items-center gap-2">
                <i class="fas fa-code text-primary"></i> Available Variables
            </h3>
            @if($template->variables && count($template->variables) > 0)
                <div class="space-y-2">
                    @foreach($template->variables as $var)
                        <button type="button" onclick="insertVariable('{{ $var }}')"
                                class="w-full flex items-center justify-between group bg-navy hover:bg-primary/10 border border-primary/5 hover:border-primary/30 rounded-xl px-4 py-2.5 transition-all text-left">
                            <code class="text-xs font-mono font-bold text-primary">{{ '{' . '{' }} {{ $var }} {{ '}' . '}' }}</code>
                            <i class="fas fa-plus text-mutedText group-hover:text-primary text-xs transition-colors"></i>
                        </button>
                    @endforeach
                </div>
                <p class="text-[11px] text-mutedText mt-3 leading-relaxed">
                    <i class="fas fa-mouse-pointer text-primary/70 mr-1"></i>
                    Click any variable to copy it to clipboard, or type it manually in the editor.
                </p>
            @else
                <p class="text-xs text-mutedText">No variables defined for this template.</p>
            @endif
        </div>

        {{-- Preview Card --}}
        <div class="bg-surface rounded-2xl border border-primary/10 shadow-sm p-5">
            <h3 class="text-sm font-black text-mainText uppercase tracking-widest mb-3 flex items-center gap-2">
                <i class="fas fa-envelope text-primary"></i> Template Info
            </h3>
            <div class="space-y-3">
                <div class="bg-navy rounded-xl px-4 py-3 border border-primary/5">
                    <p class="text-[10px] font-bold text-mutedText uppercase tracking-widest mb-1">Template Key</p>
                    <code class="text-xs font-mono text-primary">{{ $template->key }}</code>
                </div>
                <div class="bg-navy rounded-xl px-4 py-3 border border-primary/5">
                    <p class="text-[10px] font-bold text-mutedText uppercase tracking-widest mb-1">Last Updated</p>
                    <p class="text-xs text-mainText font-bold">{{ $template->updated_at->format('d M Y, h:i A') }}</p>
                </div>
            </div>
        </div>

        {{-- Tips --}}
        <div class="bg-surface rounded-2xl border border-yellow-500/20 shadow-sm p-5">
            <h3 class="text-sm font-black text-yellow-400 uppercase tracking-widest mb-3 flex items-center gap-2">
                <i class="fas fa-lightbulb"></i> Tips
            </h3>
            <ul class="space-y-2 text-xs text-mutedText leading-relaxed">
                <li class="flex items-start gap-2"><i class="fas fa-check text-yellow-400 mt-0.5 shrink-0"></i> Use double curly braces: <code class="text-primary">@{{variable}}</code></li>
                <li class="flex items-start gap-2"><i class="fas fa-check text-yellow-400 mt-0.5 shrink-0"></i> Subject line supports variables too.</li>
                <li class="flex items-start gap-2"><i class="fas fa-check text-yellow-400 mt-0.5 shrink-0"></i> Keep HTML simple — use inline styles for email compatibility.</li>
                <li class="flex items-start gap-2"><i class="fas fa-check text-yellow-400 mt-0.5 shrink-0"></i> Use the HTML mode for precise control.</li>
            </ul>
        </div>
    </div>
</div>

@push('scripts')
<script>
let currentMode = 'visual';
let editorContent = @json($template->body ?? '');

function setMode(mode) {
    currentMode = mode;

    const visualDiv  = document.getElementById('visual-editor');
    const codeDiv    = document.getElementById('code-editor');
    const btnVisual  = document.getElementById('btn-visual');
    const btnCode    = document.getElementById('btn-code');
    const codeArea   = document.getElementById('body_code');

    if (mode === 'visual') {
        // Copy from code editor back to visual
        editorContent = codeArea.value;
        if (window.tinymce && tinymce.get('body_editor')) {
            tinymce.get('body_editor').setContent(editorContent);
        }
        visualDiv.classList.remove('hidden');
        codeDiv.classList.add('hidden');
        btnVisual.className = 'text-[11px] font-bold px-3 py-1.5 rounded-lg bg-primary text-white transition-all';
        btnCode.className   = 'text-[11px] font-bold px-3 py-1.5 rounded-lg bg-navy border border-primary/10 text-mutedText hover:text-primary transition-all';
    } else {
        // Copy from TinyMCE to code area
        if (window.tinymce && tinymce.get('body_editor')) {
            editorContent = tinymce.get('body_editor').getContent();
        }
        codeArea.value = editorContent;
        visualDiv.classList.add('hidden');
        codeDiv.classList.remove('hidden');
        btnCode.className   = 'text-[11px] font-bold px-3 py-1.5 rounded-lg bg-primary text-white transition-all';
        btnVisual.className = 'text-[11px] font-bold px-3 py-1.5 rounded-lg bg-navy border border-primary/10 text-mutedText hover:text-primary transition-all';
    }
}

function insertVariable(varName) {
    const tag = '{' + '{' + varName + '}' + '}';
    navigator.clipboard.writeText(tag).then(() => {
        // Also try to insert into active editor
        if (currentMode === 'visual' && window.tinymce && tinymce.get('body_editor')) {
            tinymce.get('body_editor').insertContent(tag);
        } else {
            const area = document.getElementById('body_code');
            const pos  = area.selectionStart;
            area.value = area.value.slice(0, pos) + tag + area.value.slice(pos);
            area.selectionStart = area.selectionEnd = pos + tag.length;
        }
        showToast('Copied: ' + tag);
    });
}

function showToast(msg) {
    const t = document.createElement('div');
    t.className = 'fixed bottom-6 right-6 z-50 bg-primary text-white text-sm font-bold px-5 py-3 rounded-xl shadow-2xl transition-all';
    t.innerText = msg;
    document.body.appendChild(t);
    setTimeout(() => t.remove(), 2500);
}

// Sync content on form submit
document.querySelector('form').addEventListener('submit', function() {
    if (currentMode === 'visual' && window.tinymce && tinymce.get('body_editor')) {
        document.getElementById('body_editor').value = tinymce.get('body_editor').getContent();
    }
});

// Init TinyMCE
let editorInitialized = false;
function initEditor() {
    if (editorInitialized || typeof tinymce === 'undefined') return;

    tinymce.init({
        selector: '#body_editor',
        height: 500,
        menubar: false,
        skin: 'oxide-dark',
        content_css: 'dark',
        promotion: false,
        branding: false,
        plugins: 'advlist autolink lists link image charmap preview anchor code visualblocks lists link image table help wordcount',
        toolbar: 'undo redo | blocks | bold italic underline | forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image table | code removeformat',
        content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; font-size: 14px; background: #0f172a; color: #f1f5f9; padding: 20px; }',
        setup: function(editor) {
            editor.on('init', function() {
                editor.setContent(editorContent);
                editorInitialized = true;
            });
        }
    });
}
document.addEventListener('DOMContentLoaded', initEditor);
</script>
<script src="https://cdn.jsdelivr.net/npm/tinymce@6.8.3/tinymce.min.js" onload="initEditor()"></script>
@endpush
@endsection
