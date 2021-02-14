<?php

namespace App\Controller;

use App\Entity\Classroom;
use App\Exception\ClassroomException;
use App\Repository\ClassroomRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ClassroomController
 * @package App\Controller
 * @Route("/api", name="classroom_api")
 */
class ClassroomController extends AbstractController
{
    private $requiredParameters = ['name','date','isActive'];

    /**
     * @param ClassroomRepository $classroomRepository
     * @return JsonResponse
     * @Route("/classrooms", name="classrooms", methods={"GET"})
     */
    public function getClassrooms(ClassroomRepository $classroomRepository): JsonResponse
    {
        $response = new JsonResponse();

        $classrooms = $classroomRepository->findAll();
        return $response->setData($classrooms);
    }

    /**
     * @param ClassroomRepository $classroomRepository
     * @param int $id
     * @return JsonResponse
     * @Route("/classrooms/{id}", name="classroom", methods={"GET"})
     */
    public function getClassroom(ClassroomRepository $classroomRepository, int $id): JsonResponse
    {
        $classroom = $classroomRepository->find($id);

        if(is_null($classroom))
        {
            return $this->response([
                'status' => 404,
                'error' => "Classroom not found",
            ],200);
        }

        return $this->response($classroom->jsonSerialize());
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return JsonResponse
     * @Route("/classrooms", name="classroom_post", methods={"POST"})
     */
    public function createClass(
        Request $request,
        EntityManagerInterface $entityManager
    )
    {
        try {
            $content = json_decode($request->getContent(), true);

            $this->checkRequiredParameters($content);

            $classroom = new Classroom();

            $this->setValuesForClassroom($classroom,$content);

            $entityManager->persist($classroom);
            $entityManager->flush();

            return $this->response($classroom->jsonSerialize(),200);

        }catch (\Exception $exception){
            return $this->response([
                'status' => 422,
                'error' => $exception->getMessage()
            ],422);
        }
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param ClassroomRepository $classroomRepository
     * @param $id
     * @return JsonResponse
     * @Route("/classrooms/{id}", name="classroom_put", methods={"PUT"})
     */
    public function updateClassroom(
        Request $request,
        EntityManagerInterface $entityManager,
        ClassroomRepository $classroomRepository,
        $id
    )
    {
        try {
            $classroom = $classroomRepository->find($id);

            if(is_null($classroom))
            {
                return $this->response([
                    'status' => 404,
                    'error' => "Classroom not found",
                ],404);
            }

            $content = json_decode($request->getContent(), true);

            $this->checkRequiredParameters($content);
            $this->setValuesForClassroom($classroom,$content);

            $entityManager->flush();

            return $this->response($classroom->jsonSerialize(),200);

        }catch (\Exception $exception){
            return $this->response([
                'status' => 422,
                'error' => $exception->getMessage()
            ],422);
        }
    }

    /**
     * @param EntityManagerInterface $entityManager
     * @param ClassroomRepository $classroomRepository
     * @param $id
     * @return JsonResponse
     * @Route("/classrooms/{id}", name="classroom_delete", methods={"DELETE"})
     */
    public function deleteClassroom(
        EntityManagerInterface $entityManager,
        ClassroomRepository $classroomRepository,
        $id
    )
    {
        $classroom = $classroomRepository->find($id);

        if(is_null($classroom))
        {
            return $this->response([
                'status' => 404,
                'error' => "Classroom not found",
            ],404);
        }

        $entityManager->remove($classroom);
        $entityManager->flush();

        return $this->response([
            'status' => 200,
            'errors' => "Classroom deleted successfully",
        ],200);
    }

    /**
     * @param Request $request
     * @param ClassroomRepository $classroomRepository
     * @param EntityManagerInterface $entityManager
     * @param $id
     * @return JsonResponse
     * @Route("/classrooms/activate/{id}", name="classroom_activate", methods={"PUT"})
     */
    public function activateClassroom(
        Request $request,
        ClassroomRepository $classroomRepository,
        EntityManagerInterface $entityManager,
        $id
    )
    {

        try {
            $classroom = $classroomRepository->find($id);

            if(is_null($classroom))
            {
                return $this->response([
                    'status' => 404,
                    'error' => "Classroom not found",
                ],404);
            }

            $content = json_decode($request->getContent(), true);

            if(!array_key_exists('isActive',$content))
            {
                throw new ClassroomException('Missing parameter isActive');
            }

            $isActive = $content['isActive'];

            if(!is_bool($isActive))
            {
                throw new ClassroomException('isActive must be type of boolean');
            }

            $classroom->setIsActive($isActive);
            $entityManager->flush();

            return $this->response($classroom->jsonSerialize());

        }catch (ClassroomException $exception)
        {
            return $this->response([
                'status' => 422,
                'errors' => $exception->getMessage(),
            ],200);
        }

    }

    /**
     * Returns a JSON response
     *
     * @param array $data
     * @param $status
     * @param array $headers
     * @return JsonResponse
     */
    private function response(array $data, $status = 200, $headers = [])
    {
        return new JsonResponse($data, $status, $headers);
    }

    /**
     * Check required parameters
     *
     * @param array $content
     */
    private function checkRequiredParameters(array $content)
    {
        $data = [];

        foreach ($this->requiredParameters as $parameter){
            if(!array_key_exists($parameter, $content)){
                $data[] = $parameter;
            }
        }

        if(count($data) > 0)
        {
            throw new ClassroomException("Missing parameters - ".implode(', ',$data));
        }
    }

    /**
     * Set values for classroom
     *
     * @param Classroom $classroom
     * @param array $content
     */
    private function setValuesForClassroom(Classroom $classroom, array $content)
    {

        $name = $content['name'];
        $created = $content['date'];
        $isActive = $content['isActive'];

        $classroom->setName($name);

        if(!is_bool($isActive))
        {
            throw new ClassroomException('isActive must be type of boolean');
        }

        $classroom->setIsActive($isActive);

        if(!strtotime($created))
        {
            throw new ClassroomException('date must be type of datetime');
        }

        $classroom->setCreated(new \DateTime($created));
    }
}
