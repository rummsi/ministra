<?php

namespace Ministra\Lib;

use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89;
class GitHub
{
    private $repository;
    private $owner;
    private $branch = 'master';
    public function __construct($repository_url)
    {
        \preg_match('/\\/\\/github.com\\/([^\\/]+)\\/([^\\/]+)/', $repository_url, $match);
        if (\count($match) != 3) {
            throw new \Ministra\Lib\GitHubException('Wrong repository url');
        }
        $this->owner = $match[1];
        $this->repository = $match[2];
    }
    public function getOwner()
    {
        return $this->owner;
    }
    public function getRepository()
    {
        return $this->repository;
    }
    public function getFileContent($filename)
    {
        return $this->execute('https://raw.githubusercontent.com/' . $this->owner . '/' . $this->repository . '/' . $this->branch . '/' . $filename);
    }
    private function execute($url, $api_call = false)
    {
        $ch = \curl_init($url);
        \curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        \curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        \curl_setopt($ch, CURLOPT_USERAGENT, 'stalker_portal');
        \curl_setopt($ch, CURLOPT_HEADER, 1);
        if ($api_call) {
            $cache = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('github_api_cache')->where(['url' => $url])->get()->first();
            if (!empty($cache) && $cache['etag']) {
                \curl_setopt($ch, CURLOPT_HTTPHEADER, ['If-None-Match: ' . $cache['etag']]);
            }
        }
        $response = \curl_exec($ch);
        list($headers, $response) = \explode("\r\n\r\n", $response, 2);
        $headers = $this->headersAsArray($headers);
        if ($response === false) {
            if (\curl_errno($ch) == 28) {
                throw new \Ministra\Lib\GitHubConnectionTimeout('Connection timeout. url: ' . $url . '; Error: ' . \curl_error($ch));
            }
            throw new \Ministra\Lib\GitHubConnectionFailure('Error get contents from url: ' . $url . '; Error: ' . \curl_error($ch));
        }
        $http_code = (int) \curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($http_code == 304 && !empty($cache)) {
            $result = \json_decode($cache['data'], true);
        } else {
            $result = \json_decode($response, true);
        }
        if ($result !== null) {
            $message = !empty($result['message']) ? $result['message'] : $response;
            $response = $result;
            if ($api_call && !empty($headers['etag'])) {
                if (!isset($cache)) {
                    $cache = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('github_api_cache')->where(['url' => $url])->get()->first();
                }
                $data = ['url' => $url, 'etag' => $headers['etag'], 'data' => \json_encode($result), 'updated' => 'NOW()'];
                if (empty($cache)) {
                    \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->insert('github_api_cache', $data);
                } elseif ($cache['etag'] != $headers['etag']) {
                    \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('github_api_cache', $data, ['id' => $cache['id']]);
                }
            }
        } else {
            $message = $response;
        }
        if ($http_code != 200 && $http_code > 400) {
            throw new \Ministra\Lib\GitHubError($message, $http_code);
        }
        return $response;
    }
    private function headersAsArray($header_text)
    {
        $headers = [];
        foreach (\explode("\r\n", $header_text) as $i => $line) {
            if ($i === 0) {
                $headers['http_code'] = $line;
            } else {
                list($key, $value) = \explode(':', $line);
                $headers[\strtolower($key)] = \trim($value);
            }
        }
        return $headers;
    }
    public function getReleases($limit = 10)
    {
        return $this->apiCall('https://api.github.com/repos/' . $this->owner . '/' . $this->repository . '/releases?per_page=' . $limit);
    }
    private function apiCall($url)
    {
        $json_result = $this->execute($url, true);
        if (\is_string($json_result)) {
            $result = \json_decode($json_result, true);
            if ($result === null) {
                throw new \Ministra\Lib\GitHubUnknownFormat('Result cannot be decoded. Result: ' . $json_result);
            }
        } else {
            $result = $json_result;
        }
        return $result;
    }
    public function getOwnerRepositories()
    {
        return $this->apiCall('https://api.github.com/users/' . $this->owner . '/repos');
    }
    public function setTag($tag)
    {
        $this->setBranch($tag);
    }
    public function setBranch($branch)
    {
        $this->branch = $branch;
    }
    public function setRelease($version)
    {
        $this->setBranch($version);
    }
}
