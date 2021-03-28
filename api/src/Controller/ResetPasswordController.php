<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/api/reset-password")
 */
class ResetPasswordController extends AbstractController
{
    use ResetPasswordControllerTrait;

    public function __construct(private ResetPasswordHelperInterface $resetPasswordHelper)
    {
    }

    /**
     * Display & process form to request a password reset.
     *
     * @Route("", name="app_forgot_password_request")
     */
    public function request(
        Request $request,
        MailerInterface $mailer,
        TranslatorInterface $translator
    ): Response {
        $body = json_decode($request->getContent(), true);

        if (!isset($body['email'])) {
            throw new BadRequestHttpException($translator->trans('user.email.required'));
        }

        return $this->processSendingPasswordResetEmail(
            $body['email'],
            $mailer,
            $translator
        );
    }

    /**
     * Confirmation page after a user has requested a password reset.
     *
     * @Route("/check-email", name="app_check_email")
     */
    public function checkEmail(): Response
    {
        // We prevent users from directly accessing this page
        if (null === ($resetToken = $this->getTokenObjectFromSession())) {
            return new JsonResponse([], 404);
        }

        return new JsonResponse(['resetToken' => $resetToken]);
    }

    /**
     * Validates and process the reset URL that the user clicked in their email.
     *
     * @Route("/reset/{token}", name="app_reset_password")
     */
    public function reset(
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder,
        string $token = null,
        MailerInterface $mailer,
        TranslatorInterface $translator
    ): Response {
        $this->storeTokenInSession($token);

        if (null === $token) {
            throw $this->createNotFoundException($translator->trans('user.password.reset.token.required'));
        }

        try {
            $user = $this->resetPasswordHelper->validateTokenAndFetchUser($token);
        } catch (ResetPasswordExceptionInterface $e) {
            throw new BadRequestHttpException($translator->trans('user.password.reset.token.invalid'));
        }

        
        $body = json_decode($request->getContent(), true);

        if (!isset($body['plainPassword'])) {
            return new JsonResponse([], 400);
        }

        // A password reset token should be used only once, remove it.
        $this->resetPasswordHelper->removeResetRequest($token);

        // Encode the plain password, and set it.
        $user->plainPassword = $body['plainPassword'];

        // Encode the plain password, and set it.
        $encodedPassword = $passwordEncoder->encodePassword(
            $user,
            $body['plainPassword']
        );

        $user->setPassword($encodedPassword);
        $this->getDoctrine()->getManager()->flush();

        // The session is cleaned up after the password has been changed.
        $this->cleanSessionAfterReset();

        $email = (new TemplatedEmail())
            ->from(new Address($this->getParameter('app_contact_email'), $this->getParameter('contact_email_name')))
            ->to($user->getEmail())
            ->subject($translator->trans('user.password.reset.confirmation.subject'))
            ->htmlTemplate('reset_password/confirm_reset.html.twig')
            ->context([
                'user' => $user,
            ])
        ;

        $mailer->send($email);

        return new JsonResponse();
    }

    private function processSendingPasswordResetEmail(
        string $emailFormData,
        MailerInterface $mailer,
        TranslatorInterface $translator
    ): JsonResponse {
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy([
            'email' => $emailFormData,
        ]);

        // Do not reveal whether a user account was found or not.
        if (!$user) {
            return new JsonResponse(['error' => $translator->trans('user.email.not_found')], 404);
        }

        try {
            $resetToken = $this->resetPasswordHelper->generateResetToken($user);
        } catch (ResetPasswordExceptionInterface $e) {
            return new JsonResponse(['error' => $e->getReason()], 403);
        }

        $email = (new TemplatedEmail())
            ->from(new Address($this->getParameter('app_contact_email'), $this->getParameter('contact_email_name')))
            ->to($user->getEmail())
            ->subject($translator->trans('user.password.reset.request'))
            ->htmlTemplate('reset_password/email.html.twig')
            ->context([
                'resetToken' => $resetToken,
            ])
        ;

        $mailer->send($email);

        // Store the token object in session for retrieval in check-email route.
        $this->setTokenObjectInSession($resetToken);

        return new JsonResponse();
    }
}