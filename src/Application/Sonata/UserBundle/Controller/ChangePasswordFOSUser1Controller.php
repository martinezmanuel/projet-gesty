<?php
/*
 * This file is part of the Sonata project.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Application\Sonata\UserBundle\Controller;

use FOS\UserBundle\Model\UserInterface;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Application\Sonata\UserBundle\Entity\User;

/**
 * Class ChangePasswordFOSUser1Controller
 *
 * This class is inspired from the FOS Change Password Controller
 *
 * @package Sonata\UserBundle\Controller
 *
 * @author  Hugo Briand <briand@ekino.com>
 */
class ChangePasswordFOSUser1Controller extends \Sonata\UserBundle\Controller\ChangePasswordFOSUser1Controller
{
    public function changePasswordAction()
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
        $form = $this->container->get('fos_user.change_password.form');
        $formHandler = $this->container->get('fos_user.change_password.form.handler');

        $process = $formHandler->process($user);
        if ($process) {
            $this->setFlash('fos_user_success', 'change_password.flash.success');

            // On récupère l'adresse mail de l'utilisateur pour le mailing
            $mailDestinataire = $user->getEmail();

            // Envoie du mail
            $sendMessage = \Swift_Message::newInstance()
                ->setSubject('Modification de votre mot de passe')
                ->setFrom('cryptyo@gmail.com')
                ->setTo($mailDestinataire)
                ->setBody(
                    $this->renderView(
                        'emails/changePassewordMail.html.twig',
                        array(
                            'user' => $user
                        )
                    ),
                    'text/html'
                );
            $this->get('mailer')->send($sendMessage);
            return new RedirectResponse($this->getRedirectionUrl($user));
        }
        return $this->container->get('templating')->renderResponse(
            'SonataUserBundle:ChangePassword:changePassword.html.'.$this->container->getParameter('fos_user.template.engine'),
            array('form' => $form->createView())
        );
    }
    protected function getRedirectionUrl(UserInterface $user)
    {
        return $this->container->get('router')->generate('sonata_user_security_login');
    }

}
