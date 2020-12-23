<?php


namespace App\Classe;


use Symfony\Component\Validator\Constraints as Assert;

class Contact
{
    /**
     * @Assert\NotBlank(message="Vous avez oublié l'objet.")
     * @Assert\Length(
     *     max="255", maxMessage="L'objet ne peut dépasser {{ limit }} caractères."
     * )
     * @var string
     */
    private $subject;

    /**
     * @Assert\NotBlank(message="Vous avez oublié votre nom.")
     * @Assert\Length(
     *     max="255", maxMessage="Votre nom ne peut dépasser {{ limit }} caractères."
     * )
     * @var string
     */
    private $firstname;

    /**
     * @Assert\NotBlank(message="Vous avez oublié votre nom.")
     * @Assert\Length(
     *     max="255", maxMessage="Votre nom ne peut dépasser {{ limit }} caractères."
     * )
     * @var string
     */
    private $lastname;

    /**
     * @Assert\NotBlank(message="Vous avez oublié votre message.")
     * @var string
     */
    private $message;

    /**
     * @Assert\NotBlank(message="Vous avez oublié votre email.")
     * @Assert\Email(message="Veuillez respecter le format de votre email. ex:example@gmail.com")
     * @var string
     */
    private $email;

    /**
     * @return string
     */
    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * @param string $subject
     */
    public function setSubject(string $subject): void
    {
        $this->subject = $subject;
    }

    /**
     * @return string
     */
    public function getFirstname(): string
    {
        return $this->firstname;
    }

    /**
     * @param string $firstname
     */
    public function setFirstname(string $firstname): void
    {
        $this->firstname = $firstname;
    }

    /**
     * @return string
     */
    public function getLastname(): string
    {
        return $this->lastname;
    }

    /**
     * @param string $lastname
     */
    public function setLastname(string $lastname): void
    {
        $this->lastname = $lastname;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

}
