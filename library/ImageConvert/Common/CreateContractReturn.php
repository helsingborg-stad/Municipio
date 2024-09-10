<?php 

namespace Municipio\ImageConvert\Common;

class CreateContractReturn
{
  private string $url;

  // Use constructor property promotion to automatically declare and assign properties
  public function __construct(private int $id, private bool|int|string $height, private bool|int|string $width)
  {
    $this->url = $this->createAttachmentUrl($id);
  }

  private function createAttachmentUrl(int $id): string
  {
    return wp_get_attachment_url($id);
  }

  public function getId(): int
  {
    return $this->id;
  }

  public function getUrl(): string
  {
    return $this->url;
  }

  public function getWidth(): int|bool
  {
      if(is_numeric($this->width)) {
        return (int) $this->width;
      }
      return $this->width;
  }

  public function getHeight(): int|bool
  {
    if(is_numeric($this->height)) {
      return (int) $this->height;
    }
    return $this->height;
  }

  public function factory (int $id, int|bool|string $height, int|bool|string $width): CreateContractReturn
  {
    return new CreateContractReturn($id, $height, $width);
  }
}