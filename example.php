<?php
require 'vendor/autoload.php';

use Aldo\Http\Request;
use Aldo\Lexer\Lexer;

$request = new Request('http://www.digitalartsonline.co.uk/features/interactive-design/how-clearleft-redesigned-penguin-website/');
$html = $request->fetch();

$lexer = new Lexer;
$elementManager = $lexer->transform($html);

$source = $elementManager->getElement('#siteLogo')->source();

$imgContainer = $elementManager->getElement('#ultimediaswitch');

$secondImageSource = $imgContainer->getChildren()->source();
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Digital Arts Example</title>
    </head>
    <body>
        <img src="<?php echo $source; ?>" /></br>
        <img src="http://www.digitalartsonline.co.uk/<?php echo $secondImageSource; ?>" />
    </body>
</html>
