<?php

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
    use Twig\Extension\DebugExtension;
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
    $twig->addExtension(new DebugExtension());

    $twig->addFunction(new \Twig\TwigFunction('getMac', function($action) {
        return XsrfProtection::getMac($action);
    }));



    $db = Db::getDBConnection();
    if ($db==null) {
        try {
            echo $twig->render('error.twig', array('msg' => 'ERROR: Unable to connect to the database!'));
        } catch (LoaderError | RuntimeError | SyntaxError $e) { }
        die();  // Abort further execution of the script
    }


    /*Check if logged in and get the user if he's logged inn
    $user is null if he's not logged inn, or not verified by
    admin or email not verified. If everything is OK we get
    the user from session
    */

    $user = null;
    if ($session->get('loggedin') && $session->has('User')
        && $session->get('User')->verifyUser($request)) {
        //Checks if user is verified or not
        if ($session->get('User')->isEmailVerified() == 0) {
            header("location: ".$rel."register/verify.php?do=verification");
            exit();
        }
        if ($session->get('User')->isVerifiedByAdmin() == 0) {
            header("location: ".$rel."register/verify.php?do=verification");
            exit();
        }
        $user = $session->get('User'); // get the user data
    }

?>