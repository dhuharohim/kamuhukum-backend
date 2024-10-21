<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Article Assigned for Editing</title>
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
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>New Article Assigned for Editing</h1>
        </div>
        <div class="content">
            <p>Hello {{ $editor->username }},</p>
            <p>You have been assigned as the editor for a new article.</p>

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
                    <tr>
                        <td><strong>Submission Date:</strong></td>
                        <td>{{ $article->created_at->format('F j, Y') }}</td>
                    </tr>
                </table>
            </div>

            <p style="text-align: center;">
                <a style="color: white;" href="{{ 'https://admin.kamuhukumjournal.com/submission/' . $article->id }}"
                    class="button">View
                    Article</a>
            </p>

            <p>Please review the article at your earliest convenience.</p>
            <p>Thank you for your valuable contribution to our publication process!</p>

            <p>Best regards,<br>Admin KIB Journal</p>
        </div>
    </div>
</body>

</html>
