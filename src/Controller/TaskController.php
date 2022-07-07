<?php

namespace App\Controller;

use App\Entity\Task;
use App\Repository\TaskRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends AbstractController
{
    private TaskRepository $repo;

    // premier step, je sais que je vais ajouter et modifier, du coup tout ça est a taskrepository
    public function __construct(TaskRepository $repo)
    {
        $this->repo = $repo;
    }

    #[Route('/', name: 'task.create', methods: ['POST', 'GET'])]
    // despues de Request debo hacer ctl+clic para use Symfony\Component\HttpFoundation\Request;
    public function create(Request $request): Response
    {
        // request est tout ce que a ete envoye par la requette
        $submit = $request->get('submit');

        // pour recuperer la list de taches je dois declarer un variable, je crois une tableau $tasks
        $tasks = $this->repo->findAll();

        // je dois toujours verifier si le form a ete envoyé, , si submite n'existe pas alors
        // isset est pour savoir si le button a ete clique
        if (!isset($submit)) {
            // afiche les donnees et les coordonnees
            return $this->render('task/create.html.twig', ['mytasks' => $tasks]);
        }
        // dd('ici');

        // traitement des infos
        // trim est pour pas accepter les spaces
        $inputNewTask = trim($request->get('inputNewTask'));

        if (empty($inputNewTask)){
            return $this->render('task/create.html.twig', [
                'error'=>'La tâche est requis !',
                'mytask'=> $tasks
            ]);
        }

            $task = new Task();
        $task->setNom($inputNewTask)->setChecked(false);
        // dd($task);

        $this->repo->add($task, true);
        return $this->redirect('/');

        // la vieu qui sont toujours dans le templates, cet task est dans create, c'es la list de taches
        // return $this->render('task/create.html.twig', ['mytasks'=>$tasks]);
    }

    #[Route('/tasks/edit/{id}', methods: ['POST'])]
    // je dois passer le id dans le parametre parce que je le passe dans le URL
    public function update($id)
    {
            // je cherche la tache qui corresponde a cet id
        $task = $this->repo->find($id);
        // je veriefie si elle est fait au pas, en taskRepository veo que es boolean
        $task ->setChecked(!$task->isChecked());
        // j'appelle la methode update qui est a taskrepository
        $this->repo->update();
        // une fois fait je la redire vers la page d'accueil
        return $this->redirect('/');
    }

    #[Route('/tasks/{id}', methods:['POST'])]
    public function delete($id)
    {
        $task = $this->repo->find($id);
        $this->repo->remove($task, true);

        return $this->redirect('/');
    }

    #[Route('/tasks/delete/all', methods:['POST'])]
    public function deleteAll()
    {
        $this->repo->deleteAll();
        return $this->redirect('/');
    }
}
