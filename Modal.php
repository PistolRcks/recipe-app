<!--
    Set these before including Modal.php:

	$modalId		Unique ID so modals don't conflict with eachother
    $modalAction    What action will occur when the modal is accepted
    $buttonText     Text that appears on the button
    $modalText      Text that appears in the modal box
-->
<?php
	echo
	"<div id='$modalId'>
		<form id='$modalId Form' action=\"$modalAction\">
			<button onclick='modalFunction$modalId()' type='button'>$buttonText</button>
		</form>
		<script>
			function modalFunction$modalId()
			{
				if(confirm('$modalText'))
					document.getElementById('$modalId Form').submit();
				else
					console.log('modal action cancelled');
			}
		</script>
	</div>"
?>