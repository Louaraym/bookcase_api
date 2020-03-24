<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Borrow;
use App\Repository\BookRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $faker;
    private $entityManager;
    private $bookRepository;
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder, EntityManagerInterface $entityManager,BookRepository $bookRepository)
    {
        $this->faker = Factory::create('fr_FR');
        $this->entityManager = $entityManager;
        $this->bookRepository = $bookRepository;
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $this->loadUser();
        $this->loadBorrow();

        $manager->flush();
    }

    public function loadUser(): void
    {
        $commune = [
            "78003", "78005", "78006", "78007", "78009", "78010", "78013", "78015", "78020", "78029",
            "78030", "78031", "78033", "78034", "78036", "78043", "78048", "78049", "78050", "78053", "78057",
            "78062", "78068", "78070", "78071", "78072", "78073", "78076", "78077", "78082", "78084", "78087",
            "78089", "78090", "78092", "78096", "78104", "78107", "78108", "78113", "78117", "78118"
        ];
        $genre = ['male', 'female'];

        for ($i=0; $i<30; $i++){
            $user = new User();

            $user->setFirstName($this->faker->firstName($genre[random_int(0,1)]))
                     ->setLastName($this->faker->lastName)
                      ->setAddress($this->faker->streetAddress)
                      ->setTelephone($this->faker->phoneNumber)
                      ->setEmail(strtolower($user->getLastName().'@gmail.com'))
                      ->setPassword($this->encoder->encodePassword($user, $user->getLastName()))
                       ->setCityCode($commune[random_int(0, sizeof($commune)-1)])
                    ;
            $this->addReference('user'.$i, $user);
            $this->entityManager->persist($user);
        }

        $admin = new User();
        $admin->setFirstName('Raymond')
            ->setLastName('LOUA')
            ->setEmail('admin@gmail.com')
            ->setPassword($this->encoder->encodePassword($admin, $admin->getLastName()))
            ->setRoles([User::ROLE_ADMIN]);
        $this->entityManager->persist($admin);

        $manager = new User();
        $manager->setFirstName('Agathe')
            ->setLastName('GUEMOU')
            ->setEmail('manager@gmail.com')
            ->setPassword($this->encoder->encodePassword($manager, $manager->getLastName()))
            ->setRoles([User::ROLE_MANAGER]);
        $this->entityManager->persist($manager);

        $this->entityManager->flush();
    }

    public function loadBorrow(): void
    {
        for ($i=0; $i<30; $i++){
            for ($j=0, $jMax = random_int(1, 5); $j< $jMax; $j++){
                $borrow = new Borrow();
                $book = $this->bookRepository->find(random_int(1,49));
                $borrow->setBook($book)
                        ->setUser($this->getReference('user'.$i))
                        ->setBorrowDate($this->faker->dateTimeBetween('-6 months'))
                ;

                $borrowDate = $borrow->getBorrowDate()->getTimestamp();
                $borrowExpectedReturnDate = date('Y-m-d H:m:n',strtotime('15 days', $borrowDate));
                $borrowExpectedReturnDate = \DateTime::createFromFormat('Y-m-d H:m:n',$borrowExpectedReturnDate);

                $borrow->setBorrowExpectedReturnDate($borrowExpectedReturnDate);

                if (random_int(1,3)===1){
                    $borrow->setBorrowRealReturnDate($this->faker->dateTimeInInterval($borrow->getBorrowDate(),'30 days'));
                }

                $this->entityManager->persist($borrow);
            }
        }
        $this->entityManager->flush();
    }
}
