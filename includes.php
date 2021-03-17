<?php


    $homedir = __DIR__ . '/';

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
        $errorMsg = Db::getErrorMsg();
        try {
            echo $twig->render('error.twig', array('msg' => 'ERROR: Unable to connect to the database!',
                'errorMsg' => $errorMsg));
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