<?php

namespace App\Controller;

use App\App;
use App\Core\View;
use App\Core\Controller;
use App\Model\Entity\User;
use App\Core\Http\JsonResponse;
use App\Core\Http\FileResponse;
use Dompdf\Dompdf;
use Dompdf\Options;

class HomeController extends Controller {

    protected User $user;

    protected View $view;

    public function before($action): void {
        session_start();

        if (isset($_SESSION[USER_ID])) {
            $entityManager = App::getEntityManager();
            $usersRepo = $entityManager->getRepository(User::class);
            $this->user = $usersRepo->find($_SESSION[USER_ID]);

            $this->view = new View('home.phtml', [
                'pageTitle' => 'CV Generator | ' . $this->user->getFullName(),
                'topnavBg'  => '#343a40',
                'user'      => $this->user,
            ]);
        } else {
            $this->redirectTo('login');
        }
    }

    public function home() {
        $this->view->render();
    }

    public function generateCV($id) {
        if($id != $this->user->getId()) {
            $this->redirectTo('home');
        }

        // ** Debug **
        // global $_dompdf_warnings;
        // $_dompdf_warnings = [];
        // global $_dompdf_show_warnings;
        // $_dompdf_show_warnings = true;

        $cvResourcesPath = TEMPLATES_DIR . 'cv';

        $pdfOptions = new Options();
        $pdfOptions->setIsRemoteEnabled(true);
        $pdfOptions->setChroot($cvResourcesPath);

        $cvTemplate = new View('cv/template.phtml');
        $cvTemplate->assign(['user' => $this->user]);

        $dompdf = new Dompdf($pdfOptions);
        $dompdf->setBasePath($cvResourcesPath);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->loadHtml($cvTemplate->renderToString());
        $dompdf->render();
        $dompdf->stream('cv.pdf', ['Attachment' => 0]);

        // ** Debug **
        // var_dump($_dompdf_warnings);
    }

    public function saveData() {
        if (empty($_POST['token']) || $_POST['token'] !== $_SESSION[CSRF_TOKEN]) {
            return new JsonResponse(null, null, 'Access denied' . $_SESSION[CSRF_TOKEN]);
        }

        if (!empty($_POST['base'])) {
            $baseData = $_POST['base'];

            if (!empty($baseData['email'])) {
                if (filter_var($baseData['email'], FILTER_VALIDATE_EMAIL)) {
                    $this->user->setEmail($baseData['email']);
                } else {
                    return new JsonResponse(null, null, 'Invalid email address');
                }
            }

            if(!empty($baseData['birthDate'])) {
                if($birthDate = \DateTime::createFromFormat('d.m.Y', $baseData['birthDate'])) {
                    $this->user->setBirthDate($birthDate);
                } else {
                    return new JsonResponse(null, null, 'Invalid birth date format. Required format is DD.MM.YYY');
                }
            }

            $this->user->setName($baseData['name'] ?? $this->user->getName());
            $this->user->setLastname($baseData['lastname'] ?? $this->user->getLastname());
            $this->user->setPhoneNumber($baseData['phoneNumber'] ?? $this->user->getPhoneNumber());
            $this->user->setAdressStreetAndHouseNumber($baseData['streetAndHouseNumber'] ?? $this->user->getAdressStreetAndHouseNumber());
            $this->user->setAdressTownAndZipCode($baseData['townAndZipCode'] ?? $this->user->getAdressTownAndZipCode());
            $this->user->setRodo($baseData['rodo'] ?? $this->user->getRodo());
            $this->user->setGithubLink($baseData['githubLink'] ?? $this->user->getGithubLink());
        }

        $this->user->setSkills($_POST['skills'] ?? null);
        $this->user->setInterests($_POST['interests'] ?? null);
        $this->user->setEmploymentHistory($_POST['employmentHistory'] ?? null);
        $this->user->setEducationHistory($_POST['educationHistory'] ?? null);

        App::getEntityManager()->flush();

        return new JsonResponse(null, 'Data has been saved successfully');
    }

    public function getData() {
        return new JsonResponse([
            'base'              => [
                'name'                 => $this->user->getName(),
                'lastname'             => $this->user->getLastname(),
                'email'                => $this->user->getEmail(),
                'birthDate'            => $this->user->getBirthDate()->format('d.m.Y'),
                'phoneNumber'          => $this->user->getPhoneNumber(),
                'streetAndHouseNumber' => $this->user->getAdressStreetAndHouseNumber(),
                'townAndZipCode'       => $this->user->getAdressTownAndZipCode(),
                'rodo'                 => $this->user->getRodo(),
                'githubLink'           => $this->user->getGithubLink(),
            ],
            'skills'            => $this->user->getSkills(),
            'interests'         => $this->user->getInterests(),
            'employmentHistory' => $this->user->getEmploymentHistory(),
            'educationHistory'  => $this->user->getEducationHistory(),
        ]);
    }

    public function uploadImage() {
        if(isset($_FILES['imageFile']) && $_FILES['imageFile']['error'] == UPLOAD_ERR_OK) {
            $allowedExtensions = ['jpg', 'jpeg', 'gif', 'png', 'svg'];
            $fileExtension = pathinfo($_FILES['imageFile']['name'])['extension'];

            if(!in_array($fileExtension, $allowedExtensions)) {
                return new JsonResponse(null, null, 'Invalid image file type');
            }

            $fileName = uniqid() . '__.' . $fileExtension;
            
            if(!move_uploaded_file($_FILES['imageFile']['tmp_name'], IMAGES_DIR . $fileName)) {
                return new JsonResponse(null, null, 'Image upload error [2]');
            }

            if(!empty($this->user->getImageFile())) {
                $path = IMAGES_DIR . $this->user->getImageFile();
                
                if(file_exists($path) && is_file($path) && !unlink($path)) {
                    return new JsonResponse(null, null, 'Image upload error [3]');
                }
            }

            $this->user->setImageFile($fileName);
            App::getEntityManager()->flush();
            $url = App::getRouter()->getRoutes()->get('open-image')->getUrl(['imageFile' => $fileName]);

            return new JsonResponse($url, 'Image uploaded successfully', null);
        } else {
            return new JsonResponse(null, null, 'Image upload error [1]');
        }
    }

    public function openImage($imageFile) {
        if($imageFile !== $this->user->getImageFile()) {
            $this->redirectTo('home');
        }

        return new FileResponse(IMAGES_DIR . $imageFile);
    }

    public function test($a, $b, $c) {
        echo $a . PHP_EOL;
        echo $b . PHP_EOL;
        echo $c . PHP_EOL;

        var_dump(\App\App::getRouter()->getRoute()->getRegex());
    }

}