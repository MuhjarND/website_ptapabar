<?php

namespace App\Http\Controllers\Admin;

use App\Agenda;
use App\Http\Controllers\Controller;
use App\Support\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AgendaController extends Controller
{
    public function index(Request $request)
    {
        $agendaType = $request->agenda_type;
        $agendas = Agenda::with('user')
            ->agendaType(array_key_exists($agendaType, Agenda::typeOptions()) ? $agendaType : null)
            ->orderBy('start_at')
            ->paginate(20);

        $agendaTypes = Agenda::typeOptions();

        return view('admin.agendas.index', compact('agendas', 'agendaTypes'));
    }

    public function create()
    {
        $agendaTypes = Agenda::typeOptions();

        return view('admin.agendas.create', compact('agendaTypes'));
    }

    public function store(Request $request)
    {
        $data = $this->validateAgenda($request);
        $data['slug'] = Agenda::generateUniqueSlug($data['title']);
        $data['order'] = $data['order'] ?? 0;
        $data['is_active'] = $request->has('is_active');
        $data['user_id'] = auth()->id();

        $agenda = Agenda::create($data);
        ActivityLogger::record('agenda.created', $agenda, 'Menambahkan agenda pimpinan.', ['type' => $agenda->agenda_type]);

        return redirect()->route('admin.agendas.index')->with('success', 'Agenda pimpinan berhasil ditambahkan.');
    }

    public function edit(Agenda $agenda)
    {
        $agendaTypes = Agenda::typeOptions();

        return view('admin.agendas.edit', compact('agenda', 'agendaTypes'));
    }

    public function update(Request $request, Agenda $agenda)
    {
        $data = $this->validateAgenda($request);
        $data['slug'] = Agenda::generateUniqueSlug($data['title'], $agenda->id);
        $data['order'] = $data['order'] ?? 0;
        $data['is_active'] = $request->has('is_active');

        $agenda->update($data);
        ActivityLogger::record('agenda.updated', $agenda, 'Memperbarui agenda pimpinan.', ['type' => $agenda->agenda_type]);

        return redirect()->route('admin.agendas.index')->with('success', 'Agenda pimpinan berhasil diperbarui.');
    }

    public function destroy(Agenda $agenda)
    {
        ActivityLogger::record('agenda.deleted', null, 'Menghapus agenda pimpinan.', ['title' => $agenda->title, 'type' => $agenda->agenda_type]);
        $agenda->delete();

        return redirect()->route('admin.agendas.index')->with('success', 'Agenda pimpinan berhasil dihapus.');
    }

    protected function validateAgenda(Request $request)
    {
        return $request->validate([
            'title' => 'required|string|max:255',
            'agenda_type' => ['required', Rule::in(array_keys(Agenda::typeOptions()))],
            'summary' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'organizer' => 'nullable|string|max:255',
            'start_at' => 'required|date',
            'end_at' => 'nullable|date|after_or_equal:start_at',
            'order' => 'nullable|integer|min:0',
        ]);
    }
}
