<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Command;
use App\Entity\Detail;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

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
        $user = new User();

        // Création des produits
        for($i = 0; $i < 6; $i++)
        {
            $product = new Product();
            $product->setName('product '. $i)
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


            //------------- COMMANDS -------------//
            $command = new Command();
            $command->setCreatedAt(new \DateTime())
                ->setUser($user);

            $manager->persist($command);
            $manager->flush();

            //------------- DETAILS -------------//
            $detail = new Detail();
            $detail->setQuantity(2)
                ->setProduct($product)
                ->setCommand($command);

            $manager->persist($detail);
            $manager->flush();
        }

        for($i = 5; $i < 11; $i++)
        {
            $product = new Product();
            $product->setName('product '. $i)
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
                for($i = 0; $i < 6; $i++)
                {
                    $review = new Review();
                    $review->setProduct_id($product)
                    ->setUser_id($user)
                    ->setComment('Lorem ipsum sin dolor amet. Lorem ipsum sin dolor amet. Lorem ipsum sin dolor amet')
                    ->setRating('Lorem ipsum sin dolor amet. Lorem ipsum sin dolor amet. Lorem ipsum sin dolor amet. Lorem ipsum sin dolor amet');

                    $manager->persist($review);
                    $manager->flush();

                }

            //------------- COMMANDS -------------//
            $command = new Command();
            $command->setCreatedAt(new \DateTime())
                ->setUser($user);

            $manager->persist($command);
            $manager->flush();

            //------------- DETAILS -------------//
            $detail = new Detail();
            $detail->setQuantity(2)
                ->setProduct($product)
                ->setCommand($command);

            $manager->persist($detail);
            $manager->flush();
        }





    }
}
