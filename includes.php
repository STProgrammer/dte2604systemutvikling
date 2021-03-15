<?php
    //phpinfo();
    //xdebug_info();
    $homedir = __DIR__ . '/';

    //Generate relative path string "../" or "../../" depending on where it's loaded
    $rel = substr( dirname($_SERVER['PHP_SELF']), strrpos($_SERVER['REQUEST_URI'],"dte-2604-1-21v-systemutvikling"));
    $rel = preg_replace('~[^\/]*~', '', $rel);  //Remove all except "/"
    $rel = substr($rel, 0, -1);   // remove last "/"
    $rel = str_replace('/', '../', $rel);  //Turn "/" into "../"
    $rel = $rel . (basename($_SERVER['SCRIPT_FILENAME']) == "index.php" ? "" :"../");

    spl_autoload_register(function ($class_name) {
        $homedir = __DIR__ . '/';
        if (file_exists($homedir . "classes/". $class_name .".class.php"))
        require_once ($homedir . "classes/" .$class_name . '.class.php');
    });

    require_once $homedir . 'vendor/autoload.php';

    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Session\Session;
    use Twig\Environment;
    use Twig\Loader\FilesystemLoader;
    use Twig\Error\LoaderError;
    use Twig\Error\RuntimeError;
    use Twig\Error\SyntaxError;

    //HttpFoundation in $request
    $request = Request::createFromGlobals();

    //Session starts
    if($request->hasPreviousSession()) $session = $request->getSession();
    else $session = new Session();
    $session->start();

    error_reporting(E_ALL);

    // Twig templates
    $loader = new FilesystemLoader($homedir . 'templates');
    $twig = new Environment($loader, ['debug' => true]);
    $twig->addExtension(new \Twig\Extension\DebugExtension());

    $twig->addFunction(new \Twig\TwigFunction('getMac', function($action) {
        return XsrfProtection::getMac($action);
    }));

    $db = Db::getDBConnection();
    if ($db==null) {
        $session->getFlashBag()->add('header', "ERROR: Unable to connect to the database");
        echo $twig->render('error.twig', array('msg' => 'Unable to connect to the database!'));
        die();  // Abort further execution of the script
    }
?>