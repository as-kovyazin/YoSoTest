<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;

class MainController extends AbstractController
{

    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function getUser()
    {
        return $this->security->getUser();
    }

    /**
     * @param Request $request
     * @return Request
     */
    protected function transformJsonBody(Request $request): Request
    {
        $data = json_decode($request->getContent(), true);

        if ($data === null) {
            return $request;
        }

        $request->request->replace($data);

        return $request;
    }

}