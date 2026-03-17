<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate of Completion - {{ $course->title }}</title>

    <!-- Google Fonts for premium text rendering -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@700&family=Montserrat:wght@400;600;700&family=Playfair+Display:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            color-adjust: exact !important;
        }

        body {
            background-color: #f1f5f9;
            font-family: 'Montserrat', sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 2rem;
        }

        .toolbar {
            width: 100%;
            max-width: 297mm;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            background: white;
            padding: 1rem 2rem;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
        }

        .btn {
            background: #ff5e14;
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: bold;
            font-family: 'Montserrat', sans-serif;
            cursor: pointer;
            box-shadow: 0 4px 14px 0 rgba(255, 94, 20, 0.39);
            transition: transform 0.2s, box-shadow 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 94, 20, 0.23);
        }

        .btn-outline {
            background: transparent;
            color: #1e293b;
            border: 1px solid #cbd5e1;
            box-shadow: none;
        }

        .btn-outline:hover {
            background: #f8fafc;
            border-color: #94a3b8;
        }

        .certificate-wrapper {
            position: relative;
            width: 297mm;
            height: 210mm;
            background-color: white;
            box-shadow: 0 25px 50px -12px rgb(0 0 0 / 0.25);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
        }

        /* The overlay content box */
        .certificate-content {
            position: relative;
            z-index: 10;
            text-align: center;
            width: 80%;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding-top: 15mm; /* subtle push down from top */
        }

        /* TYPOGRAPHY */
        .student-name {
            font-family: 'Cinzel', serif;
            font-size: 48pt;
            font-weight: 700;
            color: #0f172a; /* Deep elegant navy */
            margin: 10mm 0;
            letter-spacing: 0.05em;
            line-height: 1.2;
            text-transform: uppercase;
        }

        .cert-body {
            font-family: 'Playfair Display', serif;
            font-size: 16pt;
            color: #475569;
            margin: 5mm 0;
            font-style: italic;
        }

        .course-title {
            font-family: 'Montserrat', sans-serif;
            font-size: 24pt;
            font-weight: 700;
            color: #d97706; /* Elegant bronze/gold color instead of bright orange */
            max-width: 85%;
            margin: 0 auto;
            line-height: 1.3;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .footer-details {
            display: flex;
            justify-content: space-between;
            width: 100%;
            margin-top: 15mm;
            padding: 0 10mm;
            position: absolute;
            bottom: 25mm;
        }

        .footer-item {
            text-align: center;
        }

        .footer-value {
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
            font-size: 12pt;
            color: #1e293b;
            padding-bottom: 2mm;
            margin-bottom: 2mm;
            display: inline-block;
        }

        .footer-label {
            font-family: 'Montserrat', sans-serif;
            font-size: 9pt;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            font-weight: 600;
            border-top: 1px solid #cbd5e1;
            padding-top: 2mm;
            width: 60mm;
        }

        /* PRINT SPECIFIC RESET - CRITICAL FOR 0 MARGIN PDF */
        @media print {
            @page {
                size: A4 landscape;
                margin: 0mm !important; /* Forces physical printer margins to 0 */
            }
            html, body {
                width: 297mm;
                height: 210mm;
                margin: 0 !important;
                padding: 0 !important;
                background-color: white !important;
            }
            .toolbar {
                display: none !important;
            }
            .certificate-wrapper {
                width: 297mm !important;
                height: 210mm !important;
                margin: 0 !important;
                padding: 0 !important;
                box-shadow: none !important;
                border: none !important;
                transform: scale(1) !important; /* prevent any scaling */
                page-break-after: avoid;
                page-break-before: avoid;
                page-break-inside: avoid;
            }
        }

        /* Responsive scaling merely for browser viewing */
        @media screen and (max-width: 1200px) {
            .certificate-wrapper { zoom: 0.8; }
        }
        @media screen and (max-width: 900px) {
            .certificate-wrapper { zoom: 0.6; }
        }
        @media screen and (max-width: 600px) {
            .certificate-wrapper { zoom: 0.4; }
        }
    </style>
</head>
<body>

    <div class="toolbar">
        <a href="{{ route('student.certificates.index') }}" class="btn btn-outline">
            <svg style="width:20px;height:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Back to Certificates
        </a>
        <div style="display: flex; gap: 10px;">
            <button id="downloadPdfBtn" onclick="downloadPDF()" class="btn" style="background-color: #0f172a;">
                <svg style="width:20px;height:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Download PDF
            </button>
            <button onclick="window.print()" class="btn btn-outline" style="border-color: #ff5e14; color: #ff5e14;">
                <svg style="width:20px;height:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Print
            </button>
        </div>
    </div>

    <!-- Certificate Render Area -->
    <div class="certificate-wrapper" id="certificate">

        <!-- Physical Image for PDF reliability -->
        <img src="{{ $templateUrl }}" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: fill; z-index: 1;" id="certificateBg">

        <!-- Editable Overlay Content -->
        <div class="certificate-content">

            <div class="cert-body" style="font-size: 16pt; margin-bottom: 2mm;">This is to certify that</div>

            <div class="student-name">{{ strtoupper($user->name) }}</div>

            <div class="cert-body" style="font-size: 14pt; max-width: 80%; margin: 5mm auto 8mm; line-height: 1.6;">
                has successfully completed the comprehensive learning track and demonstrated exceptional dedication in
            </div>

            <div class="course-title">{{ strtoupper($course->title) }}</div>

            <!-- Positioned at the bottom corners -->
            <div class="footer-details">
                <div class="footer-item">
                    <div class="footer-value">{{ date('F d, Y') }}</div>
                    <div class="footer-label">Date of Completion</div>
                </div>
                <div class="footer-item">
                    <div class="footer-value" style="font-family: inherit;">#CERT-{{ strtoupper(substr(md5($user->id . $course->id . time()), 0, 8)) }}</div>
                    <div class="footer-label">Certificate ID</div>
                </div>
            </div>

        </div>
    </div>

    <!-- Include html2pdf.js for client-side PDF generation on mobile devices -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script>
        function downloadPDF() {
            // Update button state to show loading
            const btn = document.getElementById('downloadPdfBtn');
            const originalContent = btn.innerHTML;
            btn.innerHTML = '<svg class="animate-spin" style="width:20px;height:20px;" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Generating...';
            btn.style.opacity = '0.7';
            btn.style.pointerEvents = 'none';

            const element = document.getElementById('certificate');

            // Fix viewport scaling/zoom issues on mobile during capture
            const originalZoom = element.style.zoom;
            element.style.zoom = 1;

            const opt = {
                margin:       0,
                filename:     'Certificate-{{ str_replace(" ", "-", $course->title) }}.pdf',
                image:        { type: 'jpeg', quality: 1 },
                html2canvas:  { 
                    scale: 3, 
                    useCORS: true, 
                    allowTaint: true,
                    letterRendering: true,
                    logging: true 
                },
                jsPDF:        { unit: 'mm', format: 'a4', orientation: 'landscape' }
            };

            html2pdf().set(opt).from(element).save().then(function() {
                // Restore original state
                element.style.zoom = originalZoom;
                btn.innerHTML = originalContent;
                btn.style.opacity = '1';
                btn.style.pointerEvents = 'auto';
            }).catch(function(err) {
                console.error('PDF Generation Error:', err);
                element.style.zoom = originalZoom;
                btn.innerHTML = originalContent;
                btn.style.opacity = '1';
                btn.style.pointerEvents = 'auto';
                alert('Something went wrong while generating the PDF. Please try again.');
            });
        }
    </script>
</body>
</html>
