<?php 

require_once 'DataBase.php';
// Обработчики:
function addGuestHandler()
{
    if ( isset( $_GET["name"] ) && isset( $_GET["age"] ) && isset( $_GET["category"] ) )
    {
        addGuest( $_GET["name"], $_GET["age"], $_GET["category"] );

        returnGuests();
    }
}

function addInvitationHandler()
{
    if ( isset( $_GET["text"] ) && isset( $_GET["guests"] ) )
    {
        addInvitation( 
            array(
                "id"     => generateInvitationId(),
                "text"   => $_GET["text"],
                "guests" => $_GET["guests"]
            )
        );

        returnInvitations();
    }
}

function delGuestHandler()
{
    if ( isset( $_GET["id"] ) )
    {
        removeGuest( $_GET["id"] );

        returnAllData();
    }
}

function delInvitationHandler()
{
    if ( isset( $_GET["id"] ) )
    {
        removeInvitation( $_GET["id"] );

        returnInvitations();
    }
}

function editGuestHandler()
{
    if ( isset( $_GET["id"] ) && isset( $_GET["name"] )&& isset( $_GET["age"] ) && isset( $_GET["category"] ) )
    {
        editGuestInfo( $_GET["id"], $_GET["name"], $_GET["age"], $_GET["category"] );

        returnAllData();
    }
}

function editInvitationHandler()
{
    if ( isset( $_GET["id"] ) && isset( $_GET["text"] ) && isset( $_GET["guests"] ) )
    {
        editInvitationInfo( 
            array(
                "id"     => $_GET["id"],
                "text"   => $_GET["text"],
                "guests" => $_GET["guests"]
            )
        );

        returnInvitations();
    }
}

function getDataHandler()
{
    returnAllData();
}
// Функции подготавливающие возвращаемые данные:
function returnAllData()
{
    echo json_encode( getData(), JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT );
}

function returnGuests()
{
    echo json_encode( array( "guests" => getGuests() ), JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT );
}

function returnInvitations()
{
    echo json_encode( array( "invitations" => getInvitations() ), JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT );
}
// Обределение типа запроса:
function main($requestType)
{
    switch ($requestType)
    {
        case "GET_DATA":
        {
            getDataHandler();
            break;
        }
        case "ADD_GUEST":
        {
            addGuestHandler();
            break;
        }
        case "ADD_INVITATION":
        {
            addInvitationHandler();
            break;
        }
        case "DEL_GUEST":
        {
            delGuestHandler();
            break;
        }
        case "DEL_INVITATION":
        {
            delInvitationHandler();
            break;
        }
        case "EDIT_GUEST":
        {
            editGuestHandler();
            break;
        }
        case "EDIT_INVITATION":
        {
            editInvitationHandler();
            break;
        }
        default:
        {
            echo 'Unknown request type: '.$requestType.'<br>';
            break;
        }
    }
}
if ( isset( $_GET["request_type"] ) )
{
    main($_GET["request_type"]);
}

?>