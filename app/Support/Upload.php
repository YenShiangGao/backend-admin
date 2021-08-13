<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use App\Exceptions\Support\UploadSettingNotExistException;

/**
 * Class Upload
 * @package App\Support
 */
class Upload
{
    private string $fileDisk;
    private string $filePath;
    private UploadedFile $file;

    /**
     * Upload constructor.
     * @param string $fileDisk
     */
    public function __construct($type = '')
    {
        $setting = config('constants.filesystem.' . $type);
        if (blank($setting)) {
            throw new UploadSettingNotExistException();
        }

        $this->fileDisk = Arr::get($setting, 'file_disk');
        $this->filePath = Arr::get($setting, 'directory_name');
    }

    /**
     * @param UploadedFile $file
     * @return $this
     */
    public function setFile(UploadedFile $file): Upload
    {
        $this->file = $file;

        return $this;
    }

    /**
     * 原始上傳檔名
     *
     * @return string
     */
    public function getOriginalFileName(): string
    {
        return $this->file->getClientOriginalName();
    }

    /**
     * 產生隨機檔名
     *
     * @return string
     */
    public function generateFileName(): string
    {
        $ext = $this->file->getClientOriginalExtension();

        $date = now()->format('ymd');
        $time = now()->getPreciseTimestamp(1);
        $uniqueNo = base_convert(mt_rand(1, 9) . $time . mt_rand(10, 99), 10, 36);

        return "{$date}_{$uniqueNo}.{$ext}";
    }

    /**
     * 檔案上傳
     *
     * @param string $fileName
     * @return bool
     */
    public function storeFile(string $fileName): bool
    {
        $result = $this->file->storeAs($this->filePath, $fileName, $this->fileDisk);

        return $result !== false;
    }
}
