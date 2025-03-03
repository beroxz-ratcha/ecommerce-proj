<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chatbot</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    {{-- testtttt --}}
    <h1>Chatbot</h1>
    <input type="text" id="message" placeholder="พิมพ์ข้อความ...">
    <button onclick="sendMessage()">ส่ง</button>
    <div id="chatbox"></div>

    <script>
        function sendMessage() {
            let message = $('#message').val();
            $.post("/chat", {
                message: message,
                _token: '{{ csrf_token() }}'
            }, function(data) {
                $('#chatbox').append("<p><strong>Bot:</strong> " + data.reply + "</p>");
                $('#message').val('');
            });
        }
    </script>
</body>

</html>
