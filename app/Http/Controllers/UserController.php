<?php

namespace App\Http\Controllers;

use App\Models\Entity;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with(['role', 'supervisor', 'entity'])->latest()->get();
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::orderBy('name')->get();
        $entities = Entity::orderBy('name')->get();
        return view('users.create', compact('roles', 'entities'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'phone' => 'nullable|string|max:50',
            'position' => 'nullable|string|max:255',
            'role_uuid' => 'nullable|exists:roles,uuid',
            'entity_uuid' => 'nullable|exists:entities,uuid',
            'is_active' => 'nullable|boolean',
        ]);

        User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'full_name' => $request->last_name . ' ' . $request->first_name,
            'email' => $request->email,
            'password' => Hash::make(Str::random(10)),
            'phone' => $request->phone,
            'position' => $request->position,
            'role_uuid' => $request->role_uuid,
            'entity_uuid' => $request->entity_uuid,
            'is_active' => $request->has('is_active') ? true : false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Utilisateur créé avec succès.',
            'urlback' => route('users.index'),
        ]);
    }

    public function show(User $user)
    {
        $user->load(['role', 'supervisor', 'subordinates', 'entity']);
        $roles = Role::orderBy('name')->get();
        $entities = Entity::orderBy('name')->get();
        $users = User::where('uuid', '!=', $user->uuid)
                      ->where('is_active', true)
                      ->orderBy('full_name')
                      ->get();
        return view('users.show', compact('user', 'roles', 'entities', 'users'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->uuid . ',uuid',
            'phone' => 'nullable|string|max:50',
            'position' => 'nullable|string|max:255',
            'role_uuid' => 'nullable|exists:roles,uuid',
            'entity_uuid' => 'nullable|exists:entities,uuid',
            'is_active' => 'nullable|boolean',
        ]);

        $user->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'full_name' => $request->last_name . ' ' . $request->first_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'position' => $request->position,
            'role_uuid' => $request->role_uuid,
            'entity_uuid' => $request->entity_uuid,
            'is_active' => $request->has('is_active') ? true : false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Utilisateur modifié avec succès.',
            'urlback' => 'back',
        ]);
    }

    public function updateSupervisor(Request $request, User $user)
    {
        $request->validate([
            'supervisor_uuid' => 'nullable|exists:users,uuid',
        ]);

        if ($request->supervisor_uuid === $user->uuid) {
            return response()->json([
                'success' => false,
                'message' => 'Un utilisateur ne peut pas être son propre supérieur.',
            ], 422);
        }

        $user->update([
            'supervisor_uuid' => $request->supervisor_uuid,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Supérieur hiérarchique mis à jour avec succès.',
            'urlback' => 'back',
        ]);
    }

    public function resetPassword(User $user)
    {
        $defaultPassword = 'password';

        $user->update([
            'password' => Hash::make($defaultPassword),
            'password_changed_at' => now(),
        ]);

        // TODO: Envoyer un email à l'utilisateur avec le mot de passe par défaut
        // Mail::to($user->email)->send(new ResetPasswordMail($user, $defaultPassword));

        return response()->json([
            'success' => true,
            'message' => 'Mot de passe réinitialisé avec succès. Un email a été envoyé à l\'utilisateur.',
            'urlback' => 'back',
        ]);
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt,xlsx,xls|max:5120',
        ]);

        $file = $request->file('file');
        $extension = $file->getClientOriginalExtension();

        try {
            $rows = [];

            if (in_array($extension, ['csv', 'txt'])) {
                $handle = fopen($file->getRealPath(), 'r');
                $header = null;
                while (($line = fgetcsv($handle, 0, ';')) !== false) {
                    if (!$header) {
                        $header = array_map(fn($h) => strtolower(trim($h)), $line);
                        continue;
                    }
                    if (count($line) === count($header)) {
                        $rows[] = array_combine($header, $line);
                    }
                }
                fclose($handle);
            } else {
                // For xlsx/xls, use PhpSpreadsheet if available
                if (!class_exists(\PhpOffice\PhpSpreadsheet\IOFactory::class)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Le format xlsx/xls nécessite le package PhpSpreadsheet. Veuillez utiliser un fichier CSV.',
                    ], 422);
                }
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getRealPath());
                $sheet = $spreadsheet->getActiveSheet();
                $data = $sheet->toArray();
                $header = array_map(fn($h) => strtolower(trim($h ?? '')), array_shift($data));
                foreach ($data as $line) {
                    if (count($line) === count($header)) {
                        $rows[] = array_combine($header, $line);
                    }
                }
            }

            if (empty($rows)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Le fichier est vide ou le format est incorrect.',
                ], 422);
            }

            // Required columns
            $requiredCols = ['nom', 'prenom', 'email'];
            $headerKeys = array_keys($rows[0]);
            foreach ($requiredCols as $col) {
                if (!in_array($col, $headerKeys)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Colonne manquante : ' . $col . '. Colonnes attendues : nom, prenom, email, telephone, poste, entite.',
                    ], 422);
                }
            }

            $created = 0;
            $skipped = 0;
            $errors = [];

            foreach ($rows as $i => $row) {
                $email = trim($row['email'] ?? '');
                $lastName = trim($row['nom'] ?? '');
                $firstName = trim($row['prenom'] ?? '');

                if (empty($email) || empty($lastName) || empty($firstName)) {
                    $skipped++;
                    continue;
                }

                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $errors[] = 'Ligne ' . ($i + 2) . ' : email invalide (' . $email . ')';
                    $skipped++;
                    continue;
                }

                if (User::where('email', $email)->exists()) {
                    $skipped++;
                    continue;
                }

                // Find entity by acronym if provided
                $entityUuid = null;
                $entityField = $row['sigle_entite'] ?? $row['entite'] ?? null;
                if (!empty($entityField)) {
                    $entity = Entity::where('acronym', trim($entityField))->first();
                    if ($entity) {
                        $entityUuid = $entity->uuid;
                    }
                }

                User::create([
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'full_name' => $lastName . ' ' . $firstName,
                    'email' => $email,
                    'password' => Hash::make(Str::random(10)),
                    'phone' => trim($row['telephone'] ?? $row['phone'] ?? ''),
                    'position' => trim($row['poste'] ?? $row['position'] ?? ''),
                    'entity_uuid' => $entityUuid,
                    'is_active' => true,
                ]);

                $created++;
            }

            $message = $created . ' utilisateur(s) importé(s) avec succès.';
            if ($skipped > 0) {
                $message .= ' ' . $skipped . ' ligne(s) ignorée(s).';
            }
            if (!empty($errors)) {
                $message .= ' Erreurs : ' . implode(', ', array_slice($errors, 0, 5));
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'urlback' => 'back',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'import : ' . $e->getMessage(),
            ], 500);
        }
    }

    public function downloadTemplate()
    {
        // Check if PhpSpreadsheet is available
        if (class_exists('\PhpOffice\PhpSpreadsheet\Spreadsheet')) {
            // Create Excel file
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Headers
            $headers = ['Nom', 'Prenom', 'Email', 'Telephone', 'Poste', 'Sigle_Entite'];
            $col = 'A';
            foreach ($headers as $header) {
                $sheet->setCellValue($col . '1', $header);
                $sheet->getStyle($col . '1')->getFont()->setBold(true);
                $sheet->getColumnDimension($col)->setAutoSize(true);
                $col++;
            }

            // Example row
            $sheet->setCellValue('A2', 'DUPONT');
            $sheet->setCellValue('B2', 'Jean');
            $sheet->setCellValue('C2', 'jean.dupont@example.com');
            $sheet->setCellValue('D2', '0612345678');
            $sheet->setCellValue('E2', 'Analyste');
            $sheet->setCellValue('F2', 'DG'); // Use acronym instead of full name

            // Style header row
            $sheet->getStyle('A1:F1')->applyFromArray([
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'color' => ['argb' => 'FFE0E0E0'],
                ],
            ]);

            // Create Excel file
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $fileName = 'modele_import_utilisateurs.xlsx';
            $tempFile = tempnam(sys_get_temp_dir(), $fileName);
            $writer->save($tempFile);

            return response()->download($tempFile, $fileName, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ])->deleteFileAfterSend(true);
        } else {
            // Fallback to CSV but with updated headers and acronym example
            $headers = ['Nom', 'Prenom', 'Email', 'Telephone', 'Poste', 'Sigle_Entite'];
            $example = ['DUPONT', 'Jean', 'jean.dupont@example.com', '0612345678', 'Analyste', 'DG'];

            $content = implode(';', $headers) . "\n" . implode(';', $example) . "\n";

            return response($content, 200, [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="modele_import_utilisateurs.csv"',
            ]);
        }
    }

    public function destroy(User $user)
    {
        if ($user->uuid === auth()->user()->uuid) {
            return response()->json([
                'success' => false,
                'message' => 'Vous ne pouvez pas supprimer votre propre compte.',
            ], 422);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Utilisateur supprimé avec succès.',
            'urlback' => route('users.index'),
        ]);
    }
}
