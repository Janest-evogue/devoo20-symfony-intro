<?php

namespace App\Controller;

use App\Entity\Member;
use App\Repository\MemberRepository;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DoctrineController
 * @package App\Controller
 *
 * @Route("/doctrine")
 */
class DoctrineController extends AbstractController
{
    /**
     * @Route("/")
     */
    public function index()
    {
        return $this->render('doctrine/index.html.twig', [
            'controller_name' => 'DoctrineController',
        ]);
    }

    /**
     * @Route("/dbal")
     */
    public function dbal(Connection $dbal)
    {
        // utilisable comme PDO :
        $stmt = $dbal->query('SELECT * FROM abonne WHERE id = 1');
        dump($stmt->fetch());

        // query et fetch dans la même méthode :
        $abonne1 = $dbal->fetchAssoc(
            'SELECT * FROM abonne WHERE id = :id',
            [
                ':id' => 1
            ]
        );

        dump($abonne1);

        $abonnes = $dbal->fetchAll('SELECT * FROM abonne');

        dump($abonnes);

        $nb = $dbal->fetchColumn('SELECT count(*) FROM abonne');

        dump($nb);

        // INSERT INTO abonne (prenom) VALUES ('Laurence')
//        $dbal->insert('abonne', ['prenom' => 'Laurence']);

        // UPDATE abonne SET prenom = 'Jules' WHERE id = 1
        $dbal->update('abonne', ['prenom' => 'Jules'], ['id' => 1]);

        // DELETE FROM abonne WHERE id = 5
        $dbal->delete('abonne', ['id' => 5]);

        return $this->render('doctrine/dbal.html.twig');
    }

    /**
     * @Route("/user/{id}")
     */
    public function getOneUser(MemberRepository $repository, $id)
    {
        /*
         * Retourne un objet Member dont les attributs
         * sont settés à partir de la table member avec une clause where sur l'id
         * ou null si l'id n'existe pas dans la table
         */
        $member = $repository->find($id);

        dump($member);

        return $this->render(
            'doctrine/get_one_user.html.twig',
            [
                'member' => $member
            ]
        );
    }

    /**
     * @Route("/member/list")
     */
    public function listMembers(MemberRepository $repository)
    {
        /*
         * Retourne tous le membres de la table member
         * sous la forme d'un tableau d'objets Member
         */
//        $members = $repository->findAll();

        // pour ajouter un tri, un findBy sans filtre :
        $members = $repository->findBy(
            [],
            ['id' => 'ASC']
        );

        return $this->render(
            'doctrine/list_members.html.twig',
            [
                'members' => $members
            ]
        );
    }

    /**
     * @Route("/search-email")
     */
    public function searchEmail(Request $request, MemberRepository $repository)
    {
        $twigVariables = [];

        // if (isset($_GET['email']))
        if ($request->query->has('email')) {
            // findOneBy quand on est sûr qu'il n'y aura pas plus d'un résultat
            // retourne un objet Member ou null
            $member = $repository->findOneBy(
                ['email' => $request->query->get('email')]
            );

            $twigVariables['member'] = $member;
        }

        return $this->render(
            'doctrine/search_email.html.twig',
            $twigVariables
        );
    }

    /**
     * @Route("/member/pseudo/{pseudo}")
     */
    public function getByPseudo(MemberRepository $repository, $pseudo)
    {
        // retourne un tableaux d'objets Member filtrés
        // par le pseudo
        $members = $repository->findBy(
            ['pseudo' => $pseudo], // filtre (clause WHERE)
            ['birthdate' => 'DESC'] // optionnel, pour ajouter un ORDER BY
        );

        return $this->render(
            'doctrine/list_members.html.twig',
            [
                'members' => $members
            ]
        );

    }

    /**
     * @Route("/member/create")
     */
    public function createMember(Request $request, EntityManagerInterface $manager)
    {
        // si le formulaire a été envoyé
        if ($request->isMethod('POST')) {
            // $data contient tout $_POST
            $data = $request->request->all();

            $member = new Member();

            $member
                ->setPseudo($data['pseudo'])
                ->setEmail($data['email'])
                // le setter de birthdate attend un objet DateTime
                ->setBirthdate(new \DateTime($data['birthdate']))
            ;

            // indique au gestionnaire d'entités qu'il faudra enregistrer
            // membre au prochain flush()
            // persist() fait un insert ou un update en fonction de la valeur
            // de l'attribut id (null = insert)
            $manager->persist($member);
            // enregistrement effectif
            $manager->flush();
        }

        return $this->render('doctrine/create_member.html.twig');
    }

    /**
     * @Route("/member/search")
     */
    public function search(Request $request, MemberRepository $repository)
    {
        $twigVariables = [];

        if ($request->query->has('search')) {
            // cf méthode définie dans MemberRepository
            $members = $repository->findByPseudoOrEmail(
                $request->query->get('search')
            );

            $twigVariables['members'] = $members;
        }

        return $this->render(
            'doctrine/search.html.twig',
            $twigVariables
        );
    }
}
