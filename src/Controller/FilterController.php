<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class FilterController
 * @package App\Controller
 *
 * @Route("/filter")
 */
class FilterController extends AbstractBaseController
{
    /**
     * @param Request $request
     * @Route("", methods={"POST"})
     * python3 evaluate.py --checkpoint /Users/hakanugras/Downloads/checkpoint_new\ \(31\) --in-path /Users/hakanugras/Desktop/unnamed.jpg --out-path /Users/hakanugras/Desktop/Output
     *
     * python3 /Users/canberkgecgel/Desktop/Cartoona-Me/CartoonMe/cartoonme-python/evaluate.py --checkpoint /Users/canberkgecgel/Desktop/Cartoona-Me/CartoonMe/cartoonme-python/checkpoints/}checkpoint_new\ \(31\) --in-path /Users/canberkgecgel/Desktop/Cartoona-Me/input/selfie.jpg --out-path /Users/canberkgecgel/Desktop/Cartoona-Me/output
     */
    public function index(Request $request)
    {
        try {
            $command = escapeshellcmd('/usr/custom/test.py');
            $output = shell_exec($command);
            echo $output;


            $command = 'python3 /Users/canberkgecgel/Desktop/Cartoona-Me/CartoonMe/cartoonme-python/evaluate.py --checkpoint /Users/canberkgecgel/Desktop/checkpoints/checkpoint_new\ \(29\) --in-path /Users/canberkgecgel/Desktop/Cartoona-Me/input/selfie.jpg --out-path /Users/canberkgecgel/Desktop/Cartoona-Me/output';
            //$command = escapeshellcmd();
            //shell_exec("cd");
            chdir(__DIR__);
            dd(__DIR__);
            $output = shell_exec($command);
            dd("a: " . $output);
        }catch (\Exception $exception){
            dd($exception);
        }

    }
}