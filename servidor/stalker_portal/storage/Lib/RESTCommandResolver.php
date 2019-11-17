<?php

namespace Ministra\Storage\Lib;

class RESTCommandResolver
{
    private $classMap = array('karaoke' => \Ministra\Storage\Lib\RESTCommandKaraoke::class, 'recorder' => \Ministra\Storage\Lib\RESTCommandRecorder::class, 'remote_pvr' => \Ministra\Storage\Lib\RESTCommandRemotePvr::class, 'tv_archive_recorder' => \Ministra\Storage\Lib\RESTCommandTvArchiveRecorder::class, 'vclub' => \Ministra\Storage\Lib\RESTCommandVclub::class, 'vclub_md5_checker' => \Ministra\Storage\Lib\RESTCommandVclubMd5Checker::class);
    public function getCommand(\Ministra\Storage\Lib\RESTRequest $request)
    {
        $resource = null;
        if (!isset($this->classMap[$request->getResource()])) {
            foreach (\explode('_', $request->getResource()) as $part) {
                $resource .= \ucfirst($part);
            }
        }
        $class = null === $resource ? $this->classMap[$request->getResource()] : __NAMESPACE__ . '\\' . $resource;
        if (\class_exists($class)) {
            return new $class();
        }
        throw new \Ministra\Storage\Lib\RESTCommandResolverException('Resource "' . $class . '" does not exist');
    }
}
