<?php

namespace Municipio\Search\Index\Admin;

use PHPUnit\Framework\TestCase;
use WpService\Contracts\AddAction;
use WpService\Contracts\Checked;
use WpService\Contracts\DeletePostMeta;
use WpService\Contracts\GetPostMeta;
use WpService\Contracts\UpdatePostMeta;

class PostTest extends TestCase {
  public function testCanBeInstantiated():void {
    $post = new Post(static::getWpService());
    static::assertInstanceOf(Post::class, $post);
  }

  private static function getWpService():AddAction|Checked|GetPostMeta|DeletePostMeta|UpdatePostMeta {
    return new class implements AddAction, Checked, GetPostMeta, DeletePostMeta, UpdatePostMeta {
      
      public function addAction(string $hookName, callable $callback, int $priority = 10, int $acceptedArgs = 1): true
      {
        return true;
      }

      public function checked(mixed $checked, mixed $current = true, bool $display = true): string
      {
        throw new \Exception('Not implemented');
      }

      public function getPostMeta(int $postId, string $key = '', bool $single = false): mixed
      {
        throw new \Exception('Not implemented');
      }

      public function deletePostMeta(int $postId, string $metaKey, mixed $metaValue = ''): bool
      {
        throw new \Exception('Not implemented');
      }

      public function updatePostMeta(int $postId, string $metaKey, mixed $metaValue, mixed $prevValue = ''): int|bool
      {
        throw new \Exception('Not implemented');
      }
    };
    
  }
}