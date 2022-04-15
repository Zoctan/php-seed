<?php

namespace App\Core\Response;

/**
 * Enum mime type
 */
class MimeType
{
  public const TXT = 'text/plain; charset=utf-8';
  public const XML = 'application/xml; charset=utf-8';
  public const HTML = 'application/html; charset=utf-8';
  public const JSON = 'application/json; charset=utf-8';
  public const STREAM = 'application/octet-stream';
}
