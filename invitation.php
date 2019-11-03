<?php
require_once 'php/DataBase.php';

$text = "";
if ( isset( $_GET["id"] ) )
{
    $text = getInvitationText( $_GET["id"] );
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <!-- <meta name="viewport" content="width=600, initial-scale=1.0"> -->

    <title>Приглашение на свадьбу</title>
    <link rel="shortcut icon" type="image/x-icon" href="resources/icons/wedding.jpg" />
    
    <script type="text/javascript" src="js/jquery.js"></script>
    
    <link href="css/fonts.css" rel="stylesheet">
    <link href="css/invitation.css" rel="stylesheet">

    <link href="https://fonts.googleapis.com/css?family=Marck+Script&display=swap" rel="stylesheet">

    <script>
        const c_invitationText = `<?php echo $text ?>`;

        
        $(document).ready(function() 
        {
            let invitationHtml = c_invitationText;
            
            let $clickMe = $('.click-icon'),
            $card = $('.card');
            
            $card.on('click', function() {
                $(this).toggleClass('is-opened');
                $clickMe.toggleClass('is-hidden');
            });
            
            if ( invitationHtml !== "" )
            {
                invitationHtml = invitationHtml.replace(/<\/?[^>]+>/g, '');
                invitationHtml = invitationHtml.replace(/\n/g, '<br>');
                invitationHtml = invitationHtml.replace(/(AlyOlegWED(.ru)?)/g, '<a id="linkHolder" href="./"><div id="link">$1</div></a>');
            }
            else
            {
                invitationHtml = "Приглашение не найдено...<br> :-(";
				// DEBUG:
				/*invitationHtml = 'Дорогой друг!<br>\
Если ты читаешь эти<br> строки - значит ты для нас очень много значишь!<br>\
Мы сделали наш совместный шаг в вечность и хотим пригласить тебя разделить с нами это главное событие<br>в нашей жизни. \
Ждём тебя в отеле "Садовое кольцо" в 15:00!<br>\
И зовём с собой в музыкальное путешествие, ведь «музыка нас связала!»<br>\
С любовью,<br> \
Олег и Алёна';*/
            }

            $("#invitationText").html(invitationHtml);
        });
      
      
  </script>
</head>
<body>
    <div class="cardWrapper">
        <div class="card">
            <div class="card-page cart-page-front">
                <div class="card-page cart-page-outside">
                    <div class="invitation-title">
                        <p>Олег и Алёна</p>
                        <span>27.06.2020</span>
                    </div>
                </div>
                <div class="card-page cart-page-inside">
                    <span class="merry-christmas">
                    </span>
                </div>
            </div>
            <div class="card-page cart-page-bottom">
                <p id="invitationText"></p>
            </div>
        </div>
    </div>
  
    <span class="click-icon">
        <svg viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg"><path fill="#fff" d="M31.6 17.7V26c0 1.9-.7 3.7-2 5.1v.9c0 1.6-1.3 3-3 3h-8.4c-1.6 0-3-1.3-3-3 0-.6.5-1 1-1s1 .4 1 1c0 .5.4 1 1 1h8.4c.5 0 1-.4 1-1v-1.2-.3-.1c0-.1.1-.2.2-.3 1.1-1.1 1.7-2.5 1.7-4v-8.3c0-.3-.1-.5-.3-.7-.1-.1-.5-.4-1-.3-.4.1-.8.6-.8 1.1v2.4c0 .6-.5 1-1 1s-1-.4-1-1v-5.5c0-.3-.1-.5-.3-.7s-.4-.3-.7-.3c-.5 0-1 .5-1 1v5.5c0 .6-.5 1-1 1s-1-.4-1-1v-8.5c0-.3-.1-.5-.3-.7s-.4-.3-.7-.3c-.5 0-1 .5-1 1v8.5c0 .6-.5 1-1 1s-1-.4-1-1V7.7c0-.3-.1-.5-.3-.7-.1-.1-.5-.4-1-.3-.4.1-.8.6-.8 1.1V20c0 .4-.2.8-.6.9-.4.2-.8.1-1.1-.2L11 18.1c-.6-.6-1.6-.6-2.2.1-.5.6-.4 1.5.2 2.1l7 7c.4.4.4 1 0 1.4-.2.2-.5.3-.7.3-.3 0-.5-.1-.7-.3l-7-7.1c-1.3-1.3-1.5-3.5-.3-4.8C8 16 9 15.5 10 15.5c.9 0 1.8.4 2.5 1l.9.9V7.9c0-1.4.9-2.7 2.3-3 1-.3 2.1 0 2.8.8.6.6.9 1.3.9 2.1V9c.3-.1.7-.2 1-.2.8 0 1.5.3 2.1.9s.9 1.3.9 2.1v.2c.3-.1.7-.2 1-.2.8 0 1.5.3 2.1.9s.9 1.3.9 2.1v.2c.1 0 .2-.1.3-.1 1-.3 2.1 0 2.8.8.8.5 1.1 1.3 1.1 2z"/></svg>
    </span>
</body>