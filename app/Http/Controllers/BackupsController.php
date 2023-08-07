<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use SplFileInfo;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class BackupsController extends Controller
{
    public function backupFullDatabase()
    { 
        $server = 'localhost';
        $database = 'master';

        // Comando SQL a ejecutar
        $sqlCommand = 'EXEC makeCompleteBackup';

        // Construir el comando para ejecutar en la línea de comandos
        $command = "sqlcmd -S $server -d $database -Q \"$sqlCommand\"";

        // Ejecutar el comando en la línea de comandos
        $output = shell_exec($command);

        // Devolver la salida como respuesta JSON
        return response()->json([
            'response' => 'Backup realizado correctamente',
            ['data' => $output ]], 200);
    }

    public function restoreFullDatabase()
    { 
        $server = 'localhost';
        $database = 'master';

        // Comando SQL a ejecutar
        $sqlCommand = 'EXEC restoreFullBackup';

        // Construir el comando para ejecutar en la línea de comandos
        $command = "sqlcmd -S $server -d $database -Q \"$sqlCommand\"";

        // Ejecutar el comando en la línea de comandos
        $output = shell_exec($command);

        // Devolver la salida como respuesta JSON
        return response()->json([
            'response' => 'Backup Restaurado realizado correctamente',
            ['data' => $output ]], 200);
    }

    public function backupDifferentialDatabase()
    { 
        $server = 'localhost';
        $database = 'master';

        // Comando SQL a ejecutar
        $sqlCommand = 'EXEC makeDifferentialBackup';

        // Construir el comando para ejecutar en la línea de comandos
        $command = "sqlcmd -S $server -d $database -Q \"$sqlCommand\"";

        // Ejecutar el comando en la línea de comandos
        $output = shell_exec($command);

        // Devolver la salida como respuesta JSON
        return response()->json([
            'response' => 'Backup Diferencial realizado correctamente',
            ['data' => $output ]], 200);
    }



    public function getBackups()
    { 
        // Ruta de la carpeta de backups
        $backupFolderPath = storage_path('app/backups/DifferentialBackup');

        // Obtener la lista de archivos en la carpeta de backups
        $backupFiles = File::files($backupFolderPath);

        // Formatear los nombres de archivos como un arreglo
        $backupFileNames = [];
        foreach ($backupFiles as $file) {
            $fileInfo = new SplFileInfo($file);
            $sizeBytes = filesize($fileInfo);
            $sizeKB = $sizeBytes / 1024;
            $created = date("Y-m-d H:i:s", filectime($fileInfo));
            $backupFileDetails[] = [
                'name' => $fileInfo->getFilename(),
                'size' => $sizeKB,
                'created_at' => $created
            ];
        }

        // Devolver la lista de nombres de archivos de respaldo como respuesta JSON
        return response()->json(['backups' => $backupFileDetails], 200);
    }

    
    public function restoreDifferentialDatabase(string $id)
    { 
        $server = 'localhost';
        $database = 'master';

        // Comando SQL a ejecutar
        $sqlCommand = "EXEC restoreDifferentialBackup @DifferentialBackupPath = 'C:/laragon/www/agrimarket_api/storage/app/backups/DifferentialBackup/".$id."';";

        // Construir el comando para ejecutar en la línea de comandos
        $command = "sqlcmd -S $server -d $database -Q \"$sqlCommand\"";

        // Ejecutar el comando en la línea de comandos
        $output = shell_exec($command);

        $datosUtf8 = utf8_encode($output);
        // Devolver la salida como respuesta JSON
        return response()->json([
            'response' => 'Backup Diferencial realizado correctamente',
            ['data' => $datosUtf8 ]], 200);
    }

    
    public function deleteDifferentialDatabase(string $id)
    { 
        $file = "C:/laragon/www/agrimarket_api/storage/app/backups/DifferentialBackup/".$id;
        if (File::exists($file)) {
            // Eliminar el archivo
            if (File::delete($file))
                return response()->json(['response' => 'Backup Diferencial eliminado correctamente'], 200);
            else
                return response()->json(['response' => 'Error Inesperado'], 500);
        } else {
            return response()->json(['response' => 'No existe el archivo'], 500);
        }
       
    }

}

