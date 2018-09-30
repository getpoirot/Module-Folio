<?php
namespace Module\Folio\RenderStrategy\JsonRenderer;


class ProfileRenderHydrate
    extends ProfileBasicRenderHydrate
{
    function getUser()
    {
        $r = parent::getUser();
        $r['profile'] = [
            'bio'      => @$this->profile['description'],
            'gender'   => @$this->profile['content']['gender'],
            'primary'   => @$this->profile['content']['as_primary'],
            // TODO With privacy interaction
            'location' => (isset($this->profile['content']['location'])) ? [
                'caption' => $this->profile['content']['location']['caption'],
                'geo'     => [
                    'lon' => $this->profile['content']['location']['geo'][0],
                    'lat' => $this->profile['content']['location']['geo'][1],
                ],
            ] : null,
            'birthday' => (isset($this->profile['content']['birthday'])) ? [
                'datetime'  => $this->profile['content']['birthday'],
                'timestamp' => $this->profile['content']['birthday']->getTimestamp(),
            ] : null,
            'datetime_created' => (isset($this->profile['datetime_created'])) ? [
                'datetime'  => $this->profile['datetime_created'],
                'timestamp' => $this->profile['datetime_created']->getTimestamp(),
            ] : null
        ];

        return $r;
    }
}
