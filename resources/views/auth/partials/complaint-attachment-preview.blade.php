<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complaint Attachment Preview</title>
    <style>
        html,
        body {
            width: 100%;
            height: 100%;
            margin: 0;
            background: #f8f9fa;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        }

        .attachment-preview-wrap {
            width: 100%;
            height: 100%;
            box-sizing: border-box;
            padding: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .attachment-preview-image {
            max-width: 100%;
            max-height: 100%;
            width: auto;
            height: auto;
            object-fit: contain;
        }

        .attachment-preview-pdf {
            width: 100%;
            height: 100%;
            border: 0;
            background: #ffffff;
        }

        .attachment-preview-fallback {
            max-width: 420px;
            padding: 1rem;
            border-radius: 12px;
            background: #ffffff;
            border: 1px solid rgba(0, 0, 0, 0.08);
            text-align: center;
            color: #495057;
            line-height: 1.45;
        }

        .attachment-preview-fallback a {
            color: #0d6efd;
            text-decoration: none;
            font-weight: 600;
        }

        .attachment-preview-fallback a:hover,
        .attachment-preview-fallback a:focus {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="attachment-preview-wrap">
        @if ($isImage)
            <img src="{{ $rawUrl }}" alt="Complaint attachment" class="attachment-preview-image">
        @elseif ($isPdf)
            <iframe src="{{ $rawUrl }}#view=FitH" class="attachment-preview-pdf" title="Complaint attachment PDF"></iframe>
        @else
            <div class="attachment-preview-fallback">
                <p>Preview is not available for this file type.</p>
                <p><strong>{{ $fileName }}</strong></p>
                <a href="{{ $rawUrl }}" target="_blank" rel="noopener">Open Attachment</a>
            </div>
        @endif
    </div>
</body>
</html>
