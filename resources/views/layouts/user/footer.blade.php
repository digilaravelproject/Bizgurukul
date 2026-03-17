<footer
    class="bg-customWhite border-t border-primary/5 py-4 px-8 text-mutedText text-[10px] font-bold uppercase tracking-widest flex flex-col md:flex-row justify-between items-center mt-auto">
    <div>&copy; {{ date('Y') }}. All Rights Reserved by Shrivardhankar Enterprises ( Skillspehle ).</div>
    <div class="flex space-x-6 mt-2 md:mt-0">
        <a href="{{ route('web.terms') }}" target="_blank" class="hover:text-primary transition-colors">Terms</a>
        <a href="{{ route('web.contact') }}" target="_blank" class="hover:text-primary transition-colors">Support</a>
         <a href="{{ route('web.privacy') }}" target="_blank" class="hover:text-primary transition-colors">Privacy</a>
     </div>
 </footer>
