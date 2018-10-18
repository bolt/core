<?php

declare(strict_types=1);

namespace Bolt\Media;

use Bolt\Configuration\Config;

/**
 * FilePond RequestHandler helper class.
 */

/*
1. get files (from $files and $post)
2. store files in tmp/ directory and give them a unique server id
3. return server id's to client
4. either client reverts upload or finalizes form
5. call revert($server_id) to remove file from tmp/ directory
6. call save() to save file to final directory
*/
class RequestHandler
{
    // the default location to save tmp files to
    private $tmp_dir;

    // regex to use for testing if a string is a file id
    private $file_id_format = '/^[0-9a-fA-F]{32}$/';

    /** @var Config */
    private $config;

    public function __construct(Config $config)
    {
        $this->config = $config;

        $this->tmp_dir = $this->config->getPath('cache', true, ['uploads']);
    }

    /**
     * @param $str
     *
     * @return bool
     */
    public function isFileId($str)
    {
        return preg_match($this->file_id_format, $str);
    }

    /**
     * @param $str
     *
     * @return bool
     */
    public function isURL($str)
    {
        return filter_var($str, FILTER_VALIDATE_URL);
    }

    /**
     * Catch all exceptions so we can return a 500 error when the server bugs out.
     */
    public function catchExceptions()
    {
        set_exception_handler('FilePond\RequestHandler::handleException');
    }

    public function handleException($ex)
    {
        // write to error log so we can still find out what's up
        error_log('Uncaught exception in class="' . get_class($ex) . '" message="' . $ex->getMessage() . '" line="' . $ex->getLine() . '"');

        // clean up buffer
        ob_end_clean();

        // server error mode go!
        http_response_code(500);
    }

    private function createItem($file, $id = null)
    {
        return new Item($file, $id);
    }

    /**
     * @param $fieldName
     *
     * @return array
     */
    public function loadFilesByField($fieldName)
    {
        // See if files are posted as JSON string (each file being base64 encoded)
        $base64Items = $this->loadBase64FormattedFiles($fieldName);

        // retrieves posted file objects
        $fileItems = $this->loadFileObjects($fieldName);

        // retrieves files already on server
        $tmpItems = $this->loadFilesFromTemp($fieldName);

        // save newly received files to temp files folder (tmp items already are in that folder)
        $this->saveAsTempFiles(array_merge($base64Items, $fileItems));

        // return items
        return array_merge($base64Items, $fileItems, $tmpItems);
    }

    private function loadFileObjects($fieldName)
    {
        $items = [];

        if (!isset($_FILES[$fieldName])) {
            return $items;
        }

        $FILE = $_FILES[$fieldName];

        if (is_array($FILE['tmp_name'])) {
            foreach ($FILE['tmp_name'] as $index => $tmpName) {
                array_push($items, $this->createItem([
                    'tmp_name' => $FILE['tmp_name'][$index],
                    'name' => $FILE['name'][$index],
                    'size' => $FILE['size'][$index],
                    'error' => $FILE['error'][$index],
                    'type' => $FILE['type'][$index],
                ]));
            }
        } else {
            array_push($items, $this->createItem($FILE));
        }

        return $items;
    }

    private function loadBase64FormattedFiles($fieldName)
    {
        /*
        // format:
        {
            "id": "iuhv2cpsu",
            "name": "picture.jpg",
            "type": "image/jpeg",
            "size": 20636,
            "metadata" : {...}
            "data": "/9j/4AAQSkZJRgABAQEASABIAA..."
        }
        */

        $items = [];

        if (!isset($_POST[$fieldName])) {
            return $items;
        }

        // Handle posted files array
        $values = $_POST[$fieldName];

        // Turn values in array if is submitted as single value
        if (!is_array($values)) {
            $values = isset($values) ? [$values] : [];
        }

        // If files are found, turn base64 strings into actual file objects
        foreach ($values as $value) {
            // suppress error messages, we'll just investigate the object later
            $obj = @json_decode($value);

            // skip values that failed to be decoded
            if (!isset($obj)) {
                continue;
            }

            // test if this is a file object (matches the object described above)
            if (!$this->isEncodedFile($obj)) {
                continue;
            }

            array_push($items, $this->createItem($this->createTempFile($obj)));
        }

        return $items;
    }

    private function isEncodedFile($obj)
    {
        return isset($obj->id) && isset($obj->data) && isset($obj->name) && isset($obj->type) && isset($obj->size);
    }

    private function loadFilesFromTemp($fieldName)
    {
        $items = [];

        if (!isset($_POST[$fieldName])) {
            return $items;
        }

        // Handle posted ids array
        $values = $_POST[$fieldName];

        // Turn values in array if is submitted as single value
        if (!is_array($values)) {
            $values = isset($values) ? [$values] : [];
        }

        // test if value is actually a file id
        foreach ($values as $value) {
            if ($this->isFileId($value)) {
                array_push($items, $this->createItem($this->getTempFile($value), $value));
            }
        }

        return $items;
    }

    public function save($items, $path = 'uploads' . \DIRECTORY_SEPARATOR)
    {
        // is list of files
        if (is_array($items)) {
            $results = [];
            foreach ($items as $item) {
                array_push($results, $this->saveFile($item, $path));
            }

            return $results;
        }

        // is single item
        return $this->saveFile($items, $path);
    }

    /**
     * @param $file_id
     *
     * @return bool
     */
    public function deleteTempFile($file_id)
    {
        return $this->deleteTempDirectory($file_id);
    }

    /**
     * @param $url
     *
     * @return array|bool
     */
    public function getRemoteURLData($url)
    {
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            $content = curl_exec($ch);
            if ($content === false) {
                throw new Exception(curl_error($ch), curl_errno($ch));
            }

            $type = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
            $length = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            curl_close($ch);

            $success = $code >= 200 && $code < 300;

            return [
                'code' => $code,
                'content' => $content,
                'type' => $type,
                'length' => $length,
                'success' => $success,
            ];
        } catch (Exception $e) {
            return null;
        }
    }

    private function saveAsTempFiles($items)
    {
        foreach ($items as $item) {
            $this->saveTempFile($item);
        }
    }

    private function saveTempFile($file)
    {
        // make sure path name is safe
        $path = $this->getSecureTempPath() . $file->getId() . \DIRECTORY_SEPARATOR;

        // Creates a secure temporary directory to store the files in
        $this->createSecureDirectory($path);

        // get source and target values
        $source = $file->getFilename();
        $target = $path . $file->getName();

        // Move uploaded file to this new secure directory
        $result = $this->moveFile($source, $target);

        // Was not saved
        if ($result !== true) {
            return $result;
        }

        // Make sure file is secure
        $this->setSecureFilePermissions($target);

        // temp file stored successfully
        return true;
    }

    public function getTempFile($fileId)
    {
        // select all files in directory except .htaccess
        foreach (glob($this->getSecureTempPath() . $fileId . \DIRECTORY_SEPARATOR . '*.*') as $file) {
            try {
                $handle = fopen($file, 'rb');
                $content = fread($handle, filesize($file));
                fclose($handle);

                return [
                    'name' => basename($file),
                    'content' => $content,
                    'type' => mime_content_type($file),
                    'length' => filesize($file),
                ];
            } catch (Exception $e) {
                return null;
            }
        }

        return false;
    }

    public function getFile($file, $path)
    {
        try {
            $filename = $path . \DIRECTORY_SEPARATOR . $file;
            $handle = fopen($filename, 'rb');
            $content = fread($handle, filesize($filename));
            fclose($handle);

            return [
                'name' => basename($filename),
                'content' => $content,
                'type' => mime_content_type($filename),
                'length' => filesize($filename),
            ];
        } catch (Exception $e) {
            return null;
        }
    }

    private function saveFile($item, $path)
    {
        // nope
        if (!isset($item)) {
            return false;
        }

        // if is file id
        if (is_string($item)) {
            return $this->moveFileById($item, $path);
        }

        // is file object

        return $this->moveFileById($item->getId(), $path, $item->getName());
    }

    private function moveFileById($fileId, $path, $fileName = null)
    {
        // select all files in directory except .htaccess
        foreach (glob($this->getSecureTempPath() . $fileId . \DIRECTORY_SEPARATOR . '*.*') as $file) {
            $source = $file;
            $target = $this->getSecurePath($path);

            $this->createDirectory($target);

            rename($source, $target . (isset($fileName) ? basename($fileName) : basename($file)));
        }

        // remove directory
        $this->deleteTempDirectory($fileId);

        // done!
        return true;
    }

    private function deleteTempDirectory($id)
    {
        @array_map('unlink', glob($this->getSecureTempPath() . $id . \DIRECTORY_SEPARATOR . '{.,}*', GLOB_BRACE));

        // remove temp directory
        @rmdir($this->getSecureTempPath() . $id);
    }

    private function createTempFile($file)
    {
        $tmp = tmpfile();
        fwrite($tmp, base64_decode($file->data, true));
        $meta = stream_get_meta_data($tmp);
        $filename = $meta['uri'];

        return [
            'error' => 0,
            'size' => filesize($filename),
            'type' => $file->type,
            'name' => $file->name,
            'tmp_name' => $filename,
            'tmp' => $tmp,
        ];
    }

    private function moveFile($source, $target)
    {
        if (is_uploaded_file($source)) {
            return move_uploaded_file($source, $target);
        }

        $tmp = fopen($source, 'rb');
        $result = file_put_contents($target, fread($tmp, filesize($source)));
        fclose($tmp);

        return $result;
    }

    private function getSecurePath($path)
    {
        return pathinfo($path)['dirname'] . \DIRECTORY_SEPARATOR . basename($path) . \DIRECTORY_SEPARATOR;
    }

    private function getSecureTempPath()
    {
        return $this->getSecurePath($this->tmp_dir);
    }

    private function setSecureFilePermissions($target)
    {
        $stat = stat(dirname($target));
        $perms = $stat['mode'] & 0000666;
        @chmod($target, $perms);
    }

    private function createDirectory($path)
    {
        if (is_dir($path)) {
            return false;
        }
        mkdir($path, 0755, true);

        return true;
    }

    private function createSecureDirectory($path)
    {
        // !! If directory already exists we assume security is handled !!

        // Test if directory already exists and correct
        if ($this->createDirectory($path)) {
            // Add .htaccess file for security purposes
            $content = '# Don\'t list directory contents
IndexIgnore *
# Disable script execution
AddHandler cgi-script .php .pl .jsp .asp .sh .cgi
Options -ExecCGI -Indexes';
            file_put_contents($path . '.htaccess', $content);
        }
    }
}
