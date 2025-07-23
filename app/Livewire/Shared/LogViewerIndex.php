<?php

namespace App\Livewire\Shared;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\File;
use Mary\Traits\Toast;
use Carbon\Carbon;

class LogViewerIndex extends Component
{
    use WithPagination, Toast;

    public $search = '';
    public $logLevel = '';
    public $fechaInicio = '';
    public $fechaFin = '';
    public $selectedLogFile = 'app.log';
    public $logContent = '';
    public $showLogContent = false;
    public $logFiles = [];
    public $page = 1;
    public $previewOpen = true;
    public $codeView = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'logLevel' => ['except' => ''],
        'fechaInicio' => ['except' => ''],
        'fechaFin' => ['except' => ''],
        'page' => ['except' => 1],
    ];

    public function mount()
    {
        $this->loadLogFiles();
        if (empty($this->selectedLogFile) || !$this->selectedLogFile) {
            $this->selectedLogFile = 'app.log';
        }
        if (empty($this->fechaInicio)) {
            $this->fechaInicio = Carbon::now()->subDays(15)->format('Y-m-d');
        }
        if (empty($this->fechaFin)) {
            $this->fechaFin = Carbon::now()->format('Y-m-d');
        }
    }

    public function loadLogFiles()
    {
        $logPath = storage_path('logs');
        $files = glob($logPath . '/*.log');
        $this->logFiles = collect($files)
            ->map(function ($path) {
                return [
                    'name' => basename($path),
                    'size' => filesize($path),
                    'modified' => Carbon::createFromTimestamp(filemtime($path)),
                ];
            })
            ->sortByDesc('modified')
            ->values()
            ->toArray();
        // Si el archivo seleccionado no existe, usar app.log si está disponible
        $fileNames = array_column($this->logFiles, 'name');
        if (!in_array($this->selectedLogFile, $fileNames)) {
            $this->selectedLogFile = in_array('app.log', $fileNames) ? 'app.log' : ($fileNames[0] ?? '');
        }
    }

    public function selectLogFile($filename)
    {
        $this->selectedLogFile = $filename;
        $this->showLogContent = false;
        $this->resetPage();
    }

    public function updatedSearch() { $this->resetPage(); }
    public function updatedLogLevel() { $this->resetPage(); }
    public function updatedFechaInicio() { $this->resetPage(); }
    public function updatedFechaFin() { $this->resetPage(); }

    public function clearFilters()
    {
        $this->reset(['search']);
        $this->fechaInicio = Carbon::now()->subDays(15)->format('Y-m-d');
        $this->fechaFin = Carbon::now()->format('Y-m-d');
        $this->resetPage();
        $this->info('Filtros limpiados correctamente');
    }

    public function nextPage() { $this->page++; }
    public function previousPage() { if ($this->page > 1) $this->page--; }
    public function resetPage() { $this->page = 1; }

    public function viewLogFile($filename)
    {
        $logPath = storage_path('logs/' . $filename);
        if (!File::exists($logPath)) {
            $this->error('Archivo de log no encontrado');
            return;
        }
        $this->selectedLogFile = $filename;
        $this->logContent = File::get($logPath);
        $this->showLogContent = true;
        $this->info('Archivo de log cargado correctamente');
    }

    public function clearLogFile($filename)
    {
        $logPath = storage_path('logs/' . $filename);
        if (!File::exists($logPath)) {
            $this->error('Archivo de log no encontrado');
            return;
        }
        File::put($logPath, '');
        $this->success('Archivo de log limpiado correctamente.');
        $this->loadLogFiles();
    }

    public function downloadLogFile($filename)
    {
        $logPath = storage_path('logs/' . $filename);
        if (!File::exists($logPath)) {
            $this->error('Archivo de log no encontrado');
            return;
        }
        return response()->download($logPath, $filename);
    }

    public function closeLogContent()
    {
        $this->showLogContent = false;
        $this->logContent = '';
    }

    public function togglePreview() { $this->previewOpen = !$this->previewOpen; }
    public function toggleCodeView() { $this->codeView = !$this->codeView; }

    public function getLogEntries()
    {
        $logPath = storage_path('logs/' . $this->selectedLogFile);
        if (!File::exists($logPath)) {
            return collect();
        }
        $content = File::get($logPath);
        $lines = explode("\n", $content);
        $entries = collect();
        foreach ($lines as $line) {
            if (empty(trim($line))) continue;
            // Nuevo regex para formato Laravel: [fecha] canal.NIVEL: mensaje contexto
            if (preg_match('/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\] ([\w.-]+)\.([A-Z]+): (.*)$/', $line, $matches)) {
                $timestamp = $matches[1];
                $channel = $matches[2];
                $level = strtoupper($matches[3]);
                $rest = $matches[4];
                // Separar mensaje y contexto JSON si existe
                $message = $rest;
                $context = null;
                if (preg_match('/^(.*?)(\{.*\})$/', $rest, $msgMatches)) {
                    $message = trim($msgMatches[1]);
                    $context = json_decode($msgMatches[2], true);
                }
                // Filtro por búsqueda
                $searchable = $message . ' ' . json_encode($context);
                if ($this->search && !str_contains(strtolower($searchable), strtolower($this->search))) {
                    continue;
                }
                // Filtro por nivel
                if ($this->logLevel && $level !== strtoupper($this->logLevel)) {
                    continue;
                }
                // Filtro por rango de fechas
                $logDate = Carbon::parse($timestamp)->format('Y-m-d');
                if ($this->fechaInicio && $logDate < $this->fechaInicio) {
                    continue;
                }
                if ($this->fechaFin && $logDate > $this->fechaFin) {
                    continue;
                }
                $entries->push([
                    'timestamp' => $timestamp,
                    'level' => $level,
                    'channel' => $channel,
                    'message' => $message,
                    'context' => $context,
                    'raw' => $line
                ]);
            }
        }
        return $entries->sortByDesc('timestamp');
    }

    public function render()
    {
        $logEntries = $this->getLogEntries();
        $paginatedEntries = $logEntries->forPage($this->page, 50);
        $statistics = [
            'total' => $logEntries->count(),
            'errors' => $logEntries->where('level', 'ERROR')->count(),
            'warnings' => $logEntries->where('level', 'WARNING')->count(),
            'info' => $logEntries->where('level', 'INFO')->count(),
            'emergency' => $logEntries->where('level', 'EMERGENCY')->count(),
        ];
        return view('livewire.shared.log-viewer-index', [
            'logEntries' => $paginatedEntries,
            'statistics' => $statistics,
            'totalPages' => ceil($logEntries->count() / 50),
            'logFileName' => $this->selectedLogFile,
            'logFileSize' => $this->logFiles[0]['size'] ?? 0,
            'logFileModified' => $this->logFiles[0]['modified'] ?? null,
        ]);
    }
}
