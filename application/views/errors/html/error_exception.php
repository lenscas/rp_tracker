<?php

defined('BASEPATH') OR exit('No direct script access allowed');

$CI = &get_instance();

$file = "errors/custom_errors/".get_class($exception);
$output = new Output();

$params = [
	"output"    => &$output,
	"exception" => $exception,
	"message"   => $message,
	"trace"     => [],

];
if(defined('SHOW_DEBUG_BACKTRACE') && SHOW_DEBUG_BACKTRACE === TRUE){
	$params["trace"] = $exception->getTrace();
}
$output->add("name" , get_class($exception));
$output->add("message" , $message);
$output->add("file" , $exception->getFile());
$output->add("line" , $exception->getLine());
$output->add("trace" , $params["trace"]);
$output->setCode(Output::CODES["GENERIC_ERROR"]);
if(file_exists(APPPATH ."views/" . $file . ".php")){
	$CI->load->view($file, $params);
}
$output->render(true);
?>

<div style="border:1px solid #990000;padding-left:20px;margin:0 0 10px 0;">

<h4>An uncaught Exception was encountered</h4>

<p>Type: <?php echo get_class($exception); ?></p>
<p>Message: <?php echo $message; ?></p>
<p>Filename: <?php echo $exception->getFile(); ?></p>
<p>Line Number: <?php echo $exception->getLine(); ?></p>

<?php if (defined('SHOW_DEBUG_BACKTRACE') && SHOW_DEBUG_BACKTRACE === TRUE): ?>

	<p>Backtrace:</p>
	<?php foreach ($exception->getTrace() as $error): ?>

		<?php if (isset($error['file']) && strpos($error['file'], realpath(BASEPATH)) !== 0): ?>

			<p style="margin-left:10px">
			File: <?php echo $error['file']; ?><br />
			Line: <?php echo $error['line']; ?><br />
			Function: <?php echo $error['function']; ?>
			</p>
		<?php endif ?>

	<?php endforeach ?>

<?php endif ?>

</div>
