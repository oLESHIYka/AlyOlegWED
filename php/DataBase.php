<?php

setlocale(LC_ALL, 'ru_RU.UTF-8');

mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');
mb_http_input('UTF-8');
mb_language('uni');
mb_regex_encoding('UTF-8');

function connect_db()
{
    $dbServer='localhost';
    $dbLogin='c926788g_wheel';
    $dbPassword='c926788g';
    $dbName='c926788g_wheel';
    $db=new mysqli($dbServer,$dbLogin,$dbPassword,$dbName);
    if ($db -> connect_error)
    {
        die('Connection has been losed! Error:'.$db->connect_error);
    }
    mysql_query("SET NAMES utf8");
	return $db;
}

function request_to_db($request)
{
	$db=connect_db();
    $row = array();     
    if ($result=$db->query($request))
    {
        if ($result->num_rows > 0)
        {
		    while($rows = $result->fetch_array(MYSQLI_ASSOC))
		    {
			    $row[] = $rows;
	        }
        }
        else
        {
    		// echo "Ничего не найдено<br>";
    	}
    }
    else
    {
        // echo 'Query error: '.$db->error.'<br>';
    }
    return $row;    	
}

function getGuests()
{
    $guests = request_to_db("SELECT * FROM guests");
    $res = array();

    foreach ($guests as $guest)
    {
        array_push(
            $res, 
            array(
                "id"       => intval($guest["id"]),
                "name"     => $guest["name"],
                "age"      => is_null($guest["age"]) ? "" : intval($guest["age"]),
                "category" => intval($guest["category"])
            )
        );
    }

    return $res;
}

function getInvitations()
{
    $invitations = request_to_db("SELECT * FROM invitations");
    $res = array();

    foreach ($invitations as $invitation)
    {
        $invitationGuests = array();
        foreach ( request_to_db('SELECT guests.id AS guest_id 
                                           FROM guests INNER JOIN 
                                                invitations 
                                                ON guests.invitation_id = invitations.id 
                                           WHERE invitations.id = '.$invitation["id"]) as $guestId )
        {
            array_push($invitationGuests, intval($guestId["guest_id"]));
        }

        array_push(
            $res, 
            array(
                "id"     => intval($invitation["id"]), 
                "text"   => $invitation["text"],
                "guests" => $invitationGuests
            )
        );
    }

    return $res;
}

function getCategories()
{
    $categories = request_to_db("SELECT * FROM guest_categories");
    $res = array();

    foreach ($categories as $category)
    {
        array_push(
            $res, 
            array(
                "id"      => intval($category["id"]),
                "caption" => $category["caption"]
            )
        );
    }

    return $res;
}

function getData()
{
    return array(
        "guests"      => getGuests(),
        "invitations" => getInvitations(),
        "categories"  => getCategories()
    );
}

function addGuest($name, $age, $category)
{
    $db=connect_db();
    $request = "INSERT INTO `guests`(`name`, `age`, `category`) VALUES (\"".$name."\", ".$age.", ".$category.")";
    if ($result=$db->query($request))
    {
        // echo 'Добавлено записей: '.$db->affected_rows.'<br>';
    }
    else
    {
        // echo 'Query error when insert guest: '.$db->error.'<br>';
    }
}

function addInvitation($invitation)
/*
invitation = 
{
    id: id0 // генерируется случайным образом в функции обработки запроса
    text: "..."
    guests: [id1, id2, ...]
}
*/
{
    $db=connect_db();
    $request = "INSERT INTO `invitations`(`id`, `text`) VALUES (".$invitation["id"].", \"".$invitation["text"]."\")";
    $guests = array();
    
    if ($result=$db->query($request))
    {
        foreach( request_to_db("SELECT `id` FROM `guests` WHERE `invitation_id` IS NULL") as $guest )
        {
            array_push($guests, $guest["id"]);
        }

        foreach ( $invitation["guests"] as $guestId )
        {
            if (in_array($guestId, $guests))
            {
                if ($result=$db->query("UPDATE `guests` SET `invitation_id` = ".$invitation["id"]." WHERE `id` = ".$guestId))
                {
                    // echo $result.'<br>';
                }
                else
                {
                    // echo 'Query error when update guests: '.$db->error.'<br>';
                }
            }
            else
            {
                // echo 'invitation already excists for guest: '.$guestId.'<br>';
            }
        }
    }
    else
    {
        // echo 'Query error when insert invitation: '.$db->error.'<br>';
    }
}

function generateInvitationId()
{
    $invitationIds = array();

    foreach (request_to_db("SELECT `id` FROM `invitations`") as $invitationId)
    {
        array_push($invitationIds, $invitationId);
    }

    $id = -1;

    if (count($invitationIds) > 0)
    {
        do
        {
            $id = mt_rand();
        } while (in_array($id, $invitationIds));
    }
    else
    {
        $id = mt_rand();
    }
    // echo 'generateInvitationId returns: '.$id.'<br>';
    return $id;
}

function removeGuest($id)
{
    $db = connect_db();

    $curGuestInvitation = request_to_db("SELECT `invitation_id` FROM `guests` WHERE `id` = ".$id);
    
    if ( count($curGuestInvitation) == 1 )
    {
        $request = "DELETE FROM `guests` WHERE `id` = ".$id;
        // echo "removing guest with id = ".$id.'<br>';
        if ($result=$db->query($request))
        {
            // echo 'Удалено записей: '.$db->affected_rows.'<br>';
            $curGuestInvitationId = $curGuestInvitation[0]["invitation_id"];
            if ( !empty($curGuestInvitationId) )
            {
                if ( count( request_to_db("SELECT `id` FROM `guests` WHERE `invitation_id` = ".$curGuestInvitationId) ) == 0 )
                {
                    removeInvitation($curGuestInvitationId);
                }
                else
                {
                    // echo 'Invitation with id = '.$curGuestInvitationId.' is still using<br>';
                }
            }
            else
            {
                // echo 'Guest with id = '.$id.' has no invitation<br>';
            }
        }
        else
        {
            // echo 'Query error when remove guest: '.$db->error.'<br>';
        }
    }
    else
    {
        // echo 'Guest with id = '.$id.' not found!<br>';
    }
}

function removeInvitation($id)
{
    $db = connect_db(); 
    
    // echo "Removing invitation with id = ".$id.'<br>';
    
    $request = "DELETE FROM `invitations` WHERE `id` = ".$id;
    if ($result=$db->query($request))
    {
        if ( $db->affected_rows > 0 )
        {
            if ( $db->query("UPDATE `guests` SET `invitation_id` = NULL WHERE `invitation_id` = ".$id) )
            {
                // echo 'Invitation seted as NULL for '.$db->affected_rows.' guests<br>';
            }
            else
            {
                // echo 'Query error when setting invitation as NULL: '.$db->error.'<br>';
            }
        }
        else
        {
            // echo 'Cannot delete absent invitation: '.$id.'<br>';
        }
    }
    else
    {
        // echo 'Query error when remove invitation: '.$db->error.'<br>';
    }
}

function editGuestInfo ($id, $name, $age, $category)
{
    $db = connect_db();
    $request = "UPDATE `guests` SET `name`=\"".$name."\",`age`=".$age.",`category`=".$category." WHERE `id` = ".$id;
    if ($result=$db->query($request))
    {
        if ($db->affected_rows == 0)
        {
            // echo 'Guest with id = '.$id.' not found<br>';
        }
    }
    else
    {
        // echo 'Query error when editing guest info: '.$db->error.'<br>';
    }
}

function editInvitationInfo ($info)
{
    $id     = $info["id"];
    $text   = $info["text"];
    $guests = $info["guests"];
    $db = connect_db();

    if ( count( request_to_db("SELECT `id` FROM `invitations` WHERE `id` = ".$id ) ) == 1 )
    {
        $request = "UPDATE `invitations` SET `text`=\"".$text."\" WHERE `id` = ".$id;
        if ($result=$db->query($request))
        {
            // Обнулить приглашение у всех гостей с этим приглашением
            $resetInvitationRequest = "UPDATE `guests` SET `invitation_id` = NULL WHERE `invitation_id` = ".$id;
            $db->query($resetInvitationRequest);
            // echo 'Приглашение обнулено для '.$db->affected_rows.' гостей<br>';
            // Для всех присланных гостей обновить приглашение
            $updateInvitationReauest = "UPDATE `guests` SET `invitation_id` = ".$id." WHERE `id` IN (".implode(',',$guests).")";
            // echo $updateInvitationReauest.'<br>';
            $db->query($updateInvitationReauest);
            // echo 'Приглашение обновлено для '.$db->affected_rows.' гостей<br>';
        }
        else
        {
            // echo 'Query error when editing guest info: '.$db->error.'<br>';
        }
    }
    else
    {
        // echo 'Invitation with id = '.$id.' not found<br>';
    }
}

function getInvitationText($id)
{
    $db = connect_db();

    if ( is_numeric( $id) )
    {
        $invitationText = request_to_db( "SELECT `text` FROM `invitations` WHERE `id` = ".$id );

        if ( count( $invitationText ) == 1 )
        {
            return $invitationText[0]["text"];
        }
    }
}

// TESTS:
// addGuest("Вова");
// removeGuest(54342);
// addInvitation(array(
//     "id" => generateInvitationId(),
//     "text" => "Some text for invitation for 2 guests",
//     "guests" => array(54343, 3)
// ));
// removeInvitation(849429145);
// editGuestInfo(54343, "Вова Тимофеев");
// editInvitationInfo(array(
//     "id" => 54321,
//     "text" => "Обновлённое второе приглашение",
//     "guests" => array(2,3)
// ));

// print_r(getGuests());
// print_r(getInvitations());
// print_r(getData());

// echo getInvitationText( "1825026657" );

?>