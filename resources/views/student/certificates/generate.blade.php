<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate of Completion - {{ $course->title }}</title>

    <!-- Google Fonts for premium text rendering -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Montserrat:wght@400;600;700&family=Pinyon+Script&display=swap" rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background-color: #f1f5f9;
            font-family: 'Montserrat', sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 2rem;
            min-height: 100vh;
        }

        .toolbar {
            width: 100%;
            max-width: 1123px; /* A4 landscape width */
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
            background: #ff5e14; /* Default brand primary */
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
            box-shadow: none;
        }

        /* Certificate Container: A4 Landscape Proportion */
        .certificate-wrapper {
            position: relative;
            width: 1123px; /* Web equivalent of A4 at 96 DPI */
            height: 794px;
            background-color: white;
            box-shadow: 0 25px 50px -12px rgb(0 0 0 / 0.25);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        /* The Template Background */
        .certificate-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: 1;
        }

        /* The overlay content box */
        .certificate-content {
            position: relative;
            z-index: 10;
            text-align: center;
            width: 80%;
            display: flex;
            flex-direction: column;
            align-items: center;
            /* Adjust padding top to push text down if template header is extremely large */
            padding-top: 50px;
        }

        .cert-title {
            font-family: 'Playfair Display', serif;
            font-size: 3.5rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 1rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }

        .cert-subtitle {
            font-size: 1.25rem;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.2em;
            margin-bottom: 2rem;
            font-weight: 600;
        }

        .student-name {
            font-family: 'Pinyon Script', cursive;
            font-size: 5rem;
            color: #0f172a;
            margin: 1rem 0;
            border-bottom: 2px solid #cbd5e1;
            display: inline-block;
            padding: 0 3rem;
            line-height: 1.2;
        }

        .cert-body {
            font-family: 'Playfair Display', serif;
            font-style: italic;
            font-size: 1.5rem;
            color: #475569;
            margin: 2rem 0 1rem;
        }

        .course-title {
            font-family: 'Montserrat', sans-serif;
            font-size: 2rem;
            font-weight: 700;
            color: #ff5e14;
            max-width: 80%;
            margin: 0 auto;
            line-height: 1.3;
        }

        .footer-details {
            display: flex;
            justify-content: space-between;
            width: 100%;
            margin-top: 4rem;
            padding: 0 2rem;
        }

        .footer-item {
            text-align: center;
            flex: 1;
        }

        .footer-value {
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
            font-size: 1.1rem;
            color: #1e293b;
            border-bottom: 1px solid #cbd5e1;
            padding-bottom: 0.5rem;
            margin-bottom: 0.5rem;
            display: inline-block;
            min-width: 200px;
        }

        .footer-label {
            font-size: 0.8rem;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            font-weight: 600;
        }

        /* Print Specific Styles */
        @media print {
            @page {
                size: A4 landscape;
                margin: 0;
            }
            body {
                background: white;
                padding: 0;
                margin: 0;
            }
            .toolbar {
                display: none;
            }
            .certificate-wrapper {
                box-shadow: none;
                width: 100vw;
                height: 100vh;
                page-break-after: avoid;
                page-break-before: avoid;
            }
        }

        /* Responsive scaling for smaller screens (Viewing only) */
        @media (max-width: 1200px) {
            .certificate-wrapper {
                transform: scale(0.8);
                transform-origin: top center;
                margin-bottom: -150px; /* Offset the scale */
            }
        }
        @media (max-width: 900px) {
            .certificate-wrapper {
                transform: scale(0.6);
                margin-bottom: -300px;
            }
        }
    </style>
</head>
<body>

    <div class="toolbar">
        <a href="{{ route('student.certificates.index') }}" class="btn btn-outline">
            <svg style="width:20px;height:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Back to Certificates
        </a>
        <button onclick="window.print()" class="btn">
            <svg style="width:20px;height:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            Save as PDF / Print
        </button>
    </div>

    <div class="certificate-wrapper" id="certificate">
        <!-- Admin Uploaded Background Template -->
        <img src="{{ $templateUrl }}" alt="Certificate Template Background" class="certificate-bg">

        <!-- Editable Overlay Content -->
        <div class="certificate-content">
            <div class="cert-subtitle">This is to certify that</div>

            <div class="student-name">{{ $user->name }}</div>

            <div class="cert-body">has successfully completed the course</div>

            <div class="course-title">{{ $course->title }}</div>

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

    <!-- Script to ensure images load before print dialog -->
    <script>
        window.onload = function() {
            // Document is fully loaded, including background
            console.log("Certificate ready for printing.");
        };
    </script>
</body>
</html>
