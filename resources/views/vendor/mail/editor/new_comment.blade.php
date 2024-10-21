<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Comment on Article</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }

        .container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
        }

        .header {
            background-color: #f4f4f4;
            padding: 20px;
            text-align: center;
        }

        .content {
            padding: 20px;
        }

        .panel {
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 20px;
        }

        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
        }

        .attachments {
            margin-top: 10px;
        }

        .attachment {
            background-color: #e9ecef;
            border: 1px solid #ced4da;
            border-radius: 4px;
            padding: 5px 10px;
            margin-bottom: 5px;
            display: inline-block;
        }

        .attachment a {
            color: #007bff;
            text-decoration: none;
        }

        .attachment a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>New Comment on Article</h1>
        </div>
        <div class="content">
            <p>Hello {{ $article->editor->username }},</p>
            <p>A new comment has been added to an article you're editing.</p>

            <h2>Article Details</h2>
            <div class="panel">
                <table width="100%" cellpadding="5">
                    <tr>
                        <td><strong>Title:</strong></td>
                        <td>{{ $article->title }}</td>
                    </tr>
                    <tr>
                        <td><strong>Authors:</strong></td>
                        <td>{{ $article->authors->map(function ($author) {return $author->given_name . ' ' . $author->family_name;})->implode(', ') }}
                        </td>
                    </tr>
                </table>
            </div>

            <h2>Comment Details</h2>
            <div class="panel">
                <p><strong>Commented by:</strong> {{ $comment->user->username }}</p>
                <p><strong>Comment:</strong> {!! $comment->comments !!}</p>
                <p><strong>Commented at:</strong> {{ $comment->commented_at }}</p>
                @if ($comment->attachments->count() > 0)
                    <div class="attachments">
                        <p><strong>Attachments:</strong></p>
                        @foreach ($comment->attachments as $attachment)
                            <div class="attachment">
                                <a href="{{ $attachment->signed_file_path }}" target="_blank">
                                    {{ $attachment->file_name }}
                                </a>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <p style="text-align: center;">
                @php
                    $url = 'https://admin.kamuhukumjournal.com/submission/' . $article->id;
                    if ($commentFor == 'law') {
                        $url = 'https://legisinsightjournal.com/dashboard/' . $article->uuid;
                    }

                    if ($commentFor == 'economy') {
                        $url = 'https://oeajournal.com/dashboard/' . $article->uuid;
                    }
                @endphp
                <a style="color: white;" href="{{ $url }}" class="button">View
                    Article</a>
            </p>

            <p>Please review the comment and any attachments at your earliest convenience.</p>
            <p>Thank you for your attention to this matter.</p>

            <p>Best regards,<br>Admin KIB Journal</p>
        </div>
    </div>
</body>

</html>
