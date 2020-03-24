<?php


namespace App\Service;


use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\Borrow;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class BorrowSubscriber implements EventSubscriberInterface
{
    private $tokenStorage;
    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => ['getAuthenticatedUser', EventPriorities::PRE_WRITE]
        ];
    }

    public function getAuthenticatedUser(ViewEvent $event): void
    {
        $value = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();
        $user = $this->tokenStorage->getToken()->getUser();

        if ($value instanceof Borrow){
            if ($method === Request::METHOD_POST){
                $value->setUser($user);
            }elseif ($method === Request::METHOD_PUT){
                if ($value->getBorrowRealReturnDate() === null){
                    $value->getBook()->setAvailable(false);
                }else{
                    $value->getBook()->setAvailable(true);
                }
            }elseif ($method === Request::METHOD_DELETE){
                $value->getBook()->setAvailable(true);
            }
        }

    }

}