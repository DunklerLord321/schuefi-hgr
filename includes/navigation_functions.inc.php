<?php

function create_list_entry_for_menu($row, $current_page) {
  global $user;
  if ( $user->isuserallowed( $row['allowed_users'] ) ) {
    if (strcmp($current_page, $row['kuerzel']) == 0 && ! strlen( $row['url_parameter'] ) > 0 ) {
      echo "<li class=\"active\" >";
    } else if ( strcmp($current_page, $row['kuerzel'] ) == 0 && strpos($_SERVER['QUERY_STRING'], $row['url_parameter'] ) !== false) {
      echo "<li class=\"active\" >";
    } else {
      echo "<li>";
    }
    if (strlen( $row['url_parameter'] ) > 0) { 
      echo "<a href=\"index.php?page=". $row['kuerzel'] . "&". $row['url_parameter'] ."\">" . $row['title'] . "</a></li>";
    } else {
      echo "<a href=\"index.php?page=". $row['kuerzel']  ."\">" . $row['title'] . "</a></li>";
    }
  }
}

function show_main_navigation($current_page) {
  global $user;
  ?>
<nav>
  <div id="nav_div">
    <button id="nav_button"><img src="img/png_menu_blue.png" alt="Menusymbol" style="width:40px;"><i style="color: white;">Menu</i></button>
      <ul class="nav_seite" id="nav_seite">
<?php
  if(!function_exists("query_db")) {
    require 'functions.inc.php';
  }
  $result = query_db("Select * FROM navigation_menu LEFT JOIN navigation ON navigation_menu.navigation_id = navigation.id");
  if ($result == false) {
    echo "Fehler";
    return false;
  } else {
    $row = $result->fetch();
    while ($row) {
      create_list_entry_for_menu($row, $current_page);
      $row = $result->fetch();            
    }
  }
?>
      </ul>
	</div>
</nav>
<script type="text/javascript">
  var account = '<?php echo $user->getaccount()?>';
  $(function() {
	$('#nav_button').click(toggle_mobile);
	$('#nav_button').mouseenter(shownav);
	function toggle_mobile() {
		if($(window).width() < 1240 && $('#nav_seite').css('opacity') > 0) {
			$('#nav_seite').fadeToggle(200);
			$('#nav').fadeToggle(200);
		}
	}
	function shownav() {
		if($(window).width() > 1240) {
			$('#content').animate({marginLeft: '22%'}, 200);
			$('#nav_seite').animate({width: '20%'}, 200);
			if(account == 'w') {
					$('#nav_seite').animate({top:'100px'},2);
			}else{
				$('#nav_seite').animate({top:'10px'},2);
			} 
			$('#nav_seite').fadeIn(200);
		}else{
			$('#nav_seite').fadeIn(200);
			$('#nav').fadeOut(200);
		}
	}
	function hidenav() {
		if($(window).width() > 1240) {
			$('#nav_seite').fadeOut(200);		
			$('#nav_seite').animate({width: '5%'}, 200);
			$('#nav_seite').animate({top:'100px'},2);
			$('#content').animate({marginLeft: '7%'}, 200);
		}else{
			$('#nav_seite').fadeOut(200);		
			$('#nav').fadeIn(200);		

		}
	}
	$('#nav_seite').mouseleave(hidenav);
	$('#content').mouseenter(hidenav);
});
</script>
<?php
}
?>
