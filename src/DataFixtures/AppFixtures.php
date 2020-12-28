<?php

namespace App\DataFixtures;

use App\Entity\Address;
use App\Entity\Carrier;
use App\Entity\Category;
use App\Entity\Command;
use App\Entity\Detail;
use App\Entity\Product;
use App\Entity\Review;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Validator\Constraints\DateTime;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        #############################################################
        # COMPOSER INSTALL (npm install) à faire après le clone
        #
        # CREER UNE BRANCHE GIT : git branch <nom_date>
        #                         git checkout <nom_de_la_branche>
        #                         git pull
        # AJOUTER LES FICHIERS AU COMMIT : git add .
        # COMMIT LE PROJET : git commit -m "<message>"
        # PUSH LE PROJET : git push <nom_de_la_branche>
        ##############################################################

        // $product = new Product();
        // $manager->persist($product);

        //Création carrier
        $carrier = new Carrier();
        $carrier->setDescription('test')
            ->setPrice(2)
            ->setName('test');
        $manager->persist($carrier);
        $manager->flush();

        // Création des catégories
        $cognac = new Category();
        $cognac->setName('Cognac')->setAlias('cognac');

        $pineau = new Category();
        $pineau->setName('Pineau')->setAlias('pineau');

        $coffret = new Category();
        $coffret->setName('Coffret')->setAlias('coffret');

        $manager->persist($cognac);
        $manager->persist($pineau);
        $manager->persist($coffret);
        $manager->flush();

        //------------- USER -------------//
        $user1 = new User();
        $user1->setcreatedAt(new \DateTime());
        $user1->setRoles(['ROLE_USER']);
        $user1->setFirstname('Hubert')
            ->setLastname('Dupont')
            ->setEmail('hubert@test.com')
            ->setPassword('test')
            ->setPhone('0606060606');

        $user2 = new User();
        $user2->setcreatedAt(new \DateTime());
        $user2->setRoles(['ROLE_USER']);
        $user2->setFirstname('Alfred')
            ->setLastname('Edouard')
            ->setEmail('alfred@test.com')
            ->setPassword('test')
            ->setPhone('0606060606');

        $manager->persist($user1);
        $manager->persist($user2);
        $manager->flush();

        //Création address
        $address1 = new Address();
        $address1->setUser($user1)
            ->setName('maison')
            ->setCity('Pekin')
            ->setAddress('10 rue des nouilles sautées')
            ->setPostalCode('12345')
            ->setCountry('Chine');

        $address2 = new Address();
        $address2->setUser($user1)
            ->setName('maison')
            ->setCity('Tokyo')
            ->setAddress('158 avenue des futomakis')
            ->setPostalCode('12345')
            ->setCountry('Japon');

        $address3 = new Address();
        $address3->setUser($user2)
            ->setName('maison')
            ->setCity('Pekin')
            ->setAddress('10 rue des nouilles sautées')
            ->setPostalCode('12345')
            ->setCountry('Chine');

        $address4 = new Address();
        $address4->setUser($user2)
            ->setName('maison')
            ->setCity('Tokyo')
            ->setAddress('158 avenue des futomakis')
            ->setPostalCode('12345')
            ->setCountry('Japon');

        $manager->persist($address1);
        $manager->persist($address2);
        $manager->persist($address3);
        $manager->persist($address4);
        $manager->flush();

        // Création des produits
        for($i = 0; $i < 6; $i++)
        {
            $product = new Product();
            $product->setName('product '. $i)
                ->setIsBest(0)
                ->setSlug('test-'.$i)
                ->setCategory($cognac)
                ->setPrice(rand(10, 150))
                ->setQuantity(75)
                ->setDescription('
                Lorem ipsum dolor sit amet, consectetur adipisicing elit. Accusamus alias animi aspernatur, consequuntur cum debitis deleniti dolores, est explicabo incidunt, molestiae pariatur possimus praesentium quasi repellat repellendus veniam voluptatem voluptates.
                ')
                ->setImage('https://via.placeholder.com/150')
                ->setYear(2000 + $i);

            $manager->persist($product);
            $manager->flush();

            //------------- REVIEWS -------------//
            for($r = 0; $r < 6; $r++)
            {
                $review = new Review();
                $review->setProduct($product)
                    ->setUser($user1)
                    ->setComment('Lorem ipsum sin dolor amet. Lorem ipsum sin dolor amet. Lorem ipsum sin dolor amet')
                    ->setRating(mt_rand(1,5));

                $manager->persist($review);
                $manager->flush();

            }

            //------------- COMMANDS -------------//
            $command = new Command();
            $command->setCreatedAt(new \DateTime())
                ->setIsPaid(0)
                ->setAddress($address1)
                ->setUser($user1)
                ->setCarrier($carrier);


            $manager->persist($command);
            $manager->flush();

            //------------- DETAILS -------------//
            for($j = 0; $j < 6; $j++)
            {
                $detail = new Detail();
                $detail->setQuantity(mt_rand(1, 10))
                    ->setProduct($product)
                    ->setCommand($command);

                $manager->persist($detail);
                $manager->flush();
            }
        }

        for($i = 5; $i < 11; $i++)
        {
            $product = new Product();
            $product->setName('product '. $i)
                ->setIsBest(0)
                ->setSlug('test-'.$i)
                ->setCategory($pineau)
                ->setPrice(rand(10, 150))
                ->setQuantity(33)
                ->setDescription('
                Lorem ipsum dolor sit amet, consectetur adipisicing elit. Accusamus alias animi aspernatur, consequuntur cum debitis deleniti dolores, est explicabo incidunt, molestiae pariatur possimus praesentium quasi repellat repellendus veniam voluptatem voluptates.
                ')
                ->setImage('https://via.placeholder.com/150')
                ->setYear(2000 + $i);

            $manager->persist($product);
            $manager->flush();

            //------------- REVIEWS -------------//

            for($r = 0; $r < 6; $r++)
            {
                $review = new Review();
                $review->setProduct($product)
                    ->setUser($user2)
                    ->setComment('Lorem ipsum sin dolor amet. Lorem ipsum sin dolor amet. Lorem ipsum sin dolor amet')
                    ->setRating(mt_rand(1,5))
                    ->setCreatedAt(new \DateTime());
                $manager->persist($review);
                $manager->flush();

            }

            //------------- COMMANDS -------------//
            $command = new Command();
            $command->setCreatedAt(new \DateTime())
                ->setIsPaid(0)
                ->setAddress($address3)
                ->setUser($user2)
                ->setCarrier($carrier);

            $manager->persist($command);
            $manager->flush();

            //------------- DETAILS -------------//
            for($j = 0; $j < 6; $j++)
            {
                $detail = new Detail();
                $detail->setQuantity(mt_rand(1, 10))
                    ->setProduct($product)
                    ->setCommand($command);

                $manager->persist($detail);
                $manager->flush();
            }

        }

    }
}
