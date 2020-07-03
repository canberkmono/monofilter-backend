<?php

namespace App\Controller;

use App\Entity\CheckPoint;
use App\Enums\HttpStatusCodes;
use App\Helpers\TokenHelper;
use App\Repository\CheckPointRepository;
use App\Service\JsonResponseService;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class FilterController
 * @package App\Controller
 *
 * @Route("/filter")
 */
class FilterController extends AbstractBaseController
{
    CONST EVALUATE_PATH = '/home/cartoonamegoogle/cartooname-py/evaluate.py';

    private $projectDir;

    public function __construct(JsonResponseService $jsonResponseService, LoggerInterface $loggerService, KernelInterface $kernel)
    {
        parent::__construct($jsonResponseService, $loggerService);
        $this->projectDir = $kernel->getProjectDir();
    }


    /**
     * @param Request $request
     * @param CheckPointRepository $checkPointRepository
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     *
     * @Route("", methods={"POST"})
     *
     * python3 /Users/canberkgecgel/Desktop/Cartoona-Me/CartoonMe/cartoonme-python/evaluate.py --checkpoint /Users/canberkgecgel/Desktop/Cartoona-Me/CartoonMe/cartoonme-python/checkpoints/}checkpoint_new\ \(31\) --in-path /Users/canberkgecgel/Desktop/Cartoona-Me/input/selfie.jpg --out-path /Users/canberkgecgel/Desktop/Cartoona-Me/output
     */
    public function index(Request $request, CheckPointRepository $checkPointRepository)
    {
        $imageContent = $request->request->get('image');
        $checkPointId = $request->request->get('checkPointId');

        try {
            $fileName = TokenHelper::generateToken();

            $inputPath = $this->projectDir . '/input/' . $fileName . '.jpg';
            file_put_contents($inputPath, file_get_contents($imageContent));

            $checkPoint = $checkPointRepository->findById($checkPointId);

            $commandText = $this->generatePythonCommand($checkPoint, $inputPath);
            //$command = escapeshellcmd($commandText);
            $output = shell_exec($commandText);


            return $this->jsonResponseService->successResponse(['output' => $output]);
        } catch (\Exception $exception) {
            return $this->jsonResponseService->errorResponse($exception->getMessage(), HttpStatusCodes::BAD_REQUEST);
        }

    }

    private function generatePythonCommand(CheckPoint $checkPoint, $inputPath)
    {
        $pythonCommand = 'python3 ' . self::EVALUATE_PATH . ' --checkpoint ' . $checkPoint->getServerPath() ;
        $pythonCommand .= ' --in-path ' . $inputPath;
        $pythonCommand .= ' --out-path ' . $this->projectDir . '/output/ 2>&1';

        return $pythonCommand;
    }
}