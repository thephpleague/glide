<?php

namespace Glide;

class Output
{
    private $storage;
    private $request;

    public function __construct(Storage $storage, Request $request)
    {
        $this->setStorage($storage);
        $this->setRequest($request);
    }

    public function setStorage(Storage $storage)
    {
        $this->storage = $storage;
    }

    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    public function output()
    {
        $this->sendHeader();
        $this->sendImage();
    }

    private function sendHeader()
    {
        header_remove();
        header('Content-Type: ' . $this->storage->getMimetype($this->request->getHash()));
        header('Content-Length: ' . $this->storage->getSize($this->request->getHash()));
        header('Expires: ' . gmdate('D, d M Y H:i:s', strtotime('+1 years')) . ' GMT');
        header('Cache-Control: public, max-age=31536000');
        header('Pragma: public');
        flush();
    }

    private function sendImage()
    {
        $this->storage->readStream($this->request->getHash());
    }
}
