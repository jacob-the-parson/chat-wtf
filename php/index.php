<?php
$settings = require( __DIR__ . "/settings.php" );
require( __DIR__ . "/database.php" );
require( __DIR__ . "/autoload.php" );

$db = get_db();
$conversation_class = get_conversation_class( $db );

$chat_id = intval( $_GET['chat_id'] ?? 0 );

$conversation = $conversation_class->find( $chat_id, $db );

if( ! $conversation ) {
    $chat_id = 0;
}

$new_chat = ! $chat_id;

$base_uri = $settings['base_uri'] ?? "";

if( $base_uri != "" ) {
    $base_uri = rtrim( $base_uri, "/" ) . "/";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Chat Website</title>
    <link rel="stylesheet" href="<?php echo $base_uri; ?>style.css" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.7.0/styles/default.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.7.0/highlight.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/showdown@2.1.0/dist/showdown.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script>
        let base_uri = '<?php echo $base_uri; ?>';
        let chat_id = <?php echo intval( $chat_id ); ?>;
        let new_chat = <?php echo $new_chat ? "true" : "false"; ?>;
    </script>
</head>
<body>
    <h1 id="header">ChatWTF</h1>
    <div id="wrapper">
        <div id="sidebar">
            <ul>
                <li class="new-chat"><a href="<?php echo $base_uri; ?>index.php">+ New chat</a></li>
            <?php
            $chats = $conversation_class->get_chats( $db );

            foreach( $chats as $chat ) {
                $id = $chat->get_id();
                $title = $chat->get_title();
                $link = $base_uri.'index.php?chat_id='.htmlspecialchars( $id );
                $delete_button = '<button class="delete" data-id="' . $id . '">X</button>';
                echo '<li><a href="'.$link.'" title="' . htmlspecialchars( $title ) . '">'.htmlspecialchars( $title ).'</a>' . $delete_button . '</li>';
            }
            ?>
            </ul>
        </div>
        <div id="chat-messages">
            <?php
            $chat_history = $chat_id ? $conversation->get_messages( $chat_id, $db ) : [];

            foreach( $chat_history as $chat_message ) {
                if( $chat_message["role"] === "system" ) {
                    continue;
                }
                $direction = $chat_message['role'] === "user" ? "outgoing" : "incoming";
                echo '<div class="chat-message '.$direction.'-message">'.htmlspecialchars( $chat_message['content'] ).'</div>';
            }
            ?>
        </div>
    </div>
    <textarea id="message-input" placeholder="Send a message..."></textarea>
    <script src="<?php echo $base_uri; ?>script.js"></script>
</body>
</html>
