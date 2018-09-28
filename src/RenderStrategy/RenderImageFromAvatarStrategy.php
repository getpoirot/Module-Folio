<?php
namespace Module\Folio\RenderStrategy;

use HttpResponse;
use Module\Folio\Models\Entities\AvatarEntity;
use Module\Folio\RenderStrategy\JsonRenderer\AvatarRenderHydrate;
use Module\HttpRenderer\RenderStrategy\aRenderStrategy;
use Poirot\Application\Sapi\Event\EventHeapOfSapi;

use Poirot\Events\Interfaces\iEvent;
use Poirot\Http\Header\FactoryHttpHeader;
use Poirot\Http\Interfaces\iHttpResponse;


class RenderImageFromAvatarStrategy
    extends aRenderStrategy
{
    const WEIGHT = 1000;

    /** @var iHttpResponse */
    protected $response;


    /**
     * Constructor.
     *
     * @param iHttpResponse $httpResponse @IoC /HttpResponse
     */
    function __construct(iHttpResponse $httpResponse)
    {
        $this->response = $httpResponse;
    }


    function __invoke($result)
    {
        if (! isset($result['avatars']) )
            throw new \RuntimeException('Unknown Result.');


        return $this->createResponseFromResult($result['avatars']);
    }

    /**
     * Initialize To Events
     *
     * - usually bind listener(s) to events
     *
     * @param EventHeapOfSapi|iEvent $events
     *
     * @return $this
     */
    function attachToEvent(iEvent $events)
    {
        // DO Nothing!!

        return $this;
    }

    /**
     * Get Content Type That Renderer Will Provide
     * exp. application/json; text/html
     *
     * @return string
     */
    function getContentType()
    {
        return 'image/jpeg;';
    }


    // ..

    /**
     * Create ViewModel From Actions Result
     *
     * @param AvatarEntity $result
     *
     * @return array|void
     */
    protected function createResponseFromResult(AvatarEntity $result = null)
    {
        return ['result' => $this->makeHttpResponseFromBinData($result)];
    }


    // ..

    /**
     * @param AvatarEntity $avatarEntity
     *
     * @return HttpResponse
     * @throws \Exception
     */
    private function makeHttpResponseFromBinData(AvatarEntity $avatarEntity)
    {
        $res = new AvatarRenderHydrate(['avatars' => $avatarEntity]);
        $res = iterator_to_array($res);


        # Build Avatar Link
        #
        if ( $res['primary'] )
            $link = $res['primary']['_link'];
        else
            // Default None-Profile Picture
            $link = \Module\HttpFoundation\getServerUrl().'/no_avatar.jpg';


        $response = $this->response;

        $response->setStatusCode(301); // permanently moved
        $response->headers()
            ->insert(FactoryHttpHeader::of(['Location' => $link, ]))
            ->insert(FactoryHttpHeader::of(['Cache-Control' => 'no-cache, no-store, must-revalidate',]))
            ->insert(FactoryHttpHeader::of(['Pragma' => 'no-cache',]))
            ->insert(FactoryHttpHeader::of(['Expires' => '0',]))
        ;


        return $response;
    }
}
