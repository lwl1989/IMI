<?php
namespace Imi\Util\Http;

use Psr\Http\Message\ResponseInterface;
use Imi\Util\Http\Consts\StatusCode;

class Response extends AbstractMessage implements ResponseInterface
{
    /**
     * 状态码
     * @var int
     */
    protected $statusCode;

    /**
     * 状态码原因短语
     * @var string
     */
    protected $reasonPhrase;

    /**
     * Trailer 列表
     *
     * @var array
     */
    protected $trailers = [];

    public function __construct()
    {
        parent::__construct('');
        $this->statusCode = StatusCode::OK;
        $this->reasonPhrase = StatusCode::getReasonPhrase($this->statusCode);
    }

    /**
     * Gets the response status code.
     *
     * The status code is a 3-digit integer result code of the server's attempt
     * to understand and satisfy the request.
     *
     * @return int Status code.
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * Return an instance with the specified status code and, optionally, reason phrase.
     *
     * If no reason phrase is specified, implementations MAY choose to default
     * to the RFC 7231 or IANA recommended reason phrase for the response's
     * status code.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * updated status and reason phrase.
     *
     * @link http://tools.ietf.org/html/rfc7231#section-6
     * @link http://www.iana.org/assignments/http-status-codes/http-status-codes.xhtml
     * @param int $code The 3-digit integer result code to set.
     * @param string $reasonPhrase The reason phrase to use with the
     *     provided status code; if none is provided, implementations MAY
     *     use the defaults as suggested in the HTTP specification.
     * @return static
     * @throws \InvalidArgumentException For invalid status code arguments.
     */
    public function withStatus($code, $reasonPhrase = '')
    {
        $self = clone $this;
        $self->statusCode = $code;
        if('' === $reasonPhrase)
        {
            $self->reasonPhrase = StatusCode::getReasonPhrase($code);
        }
        else
        {
            $self->reasonPhrase = $reasonPhrase;
        }
        return $self;
    }

    /**
     * Gets the response reason phrase associated with the status code.
     *
     * Because a reason phrase is not a required element in a response
     * status line, the reason phrase value MAY be null. Implementations MAY
     * choose to return the default RFC 7231 recommended reason phrase (or those
     * listed in the IANA HTTP Status Code Registry) for the response's
     * status code.
     *
     * @link http://tools.ietf.org/html/rfc7231#section-6
     * @link http://www.iana.org/assignments/http-status-codes/http-status-codes.xhtml
     * @return string Reason phrase; must return an empty string if none present.
     */
    public function getReasonPhrase()
    {
        return $this->reasonPhrase;
    }

    /**
     * 获取 Trailer 列表
     * 
     * @return array
     */
    public function getTrailers()
    {
        return $this->trailers;
    }

    /**
     * Trailer 是否存在
     *
     * @param string $name
     * @return bool
     */
    public function hasTrailer($name)
    {
        return isset($this->trailers[$name]);
    }

    /**
     * 获取 Trailer 值
     * 
     * @param string $name
     * @return string|null
     */
    public function getTrailer($name)
    {
        return $this->trailers[$name] ?? null;
    }

    /**
     * 获取 Trailer
     * 
     * @param string $name
     * @param string $value
     * @return static
     */
    public function withTrailer($name, $value)
    {
        $self = clone $this;
        $self->trailers[$name] = $value;
        return $self;
    }

}