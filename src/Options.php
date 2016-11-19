<?php declare(strict_types=1);

namespace ApiClients\Foundation\Transport;

class Options
{
    const DNS                     = 'dns';
    const CACHE                   = 'cache';
    const SCHEMA                  = 'schema';
    const HOST                    = 'host';
    const PORT                    = 'port';
    const PATH                    = 'path';
    const USER_AGENT              = 'user_agent';
    const HEADERS                 = 'headers';
    const MIDDLEWARE              = 'middleware';
    const DEFAULT_REQUEST_OPTIONS = 'default_request_options';
    const USER_AGENT_STRATEGY     = 'user_agent_strategy';
}
