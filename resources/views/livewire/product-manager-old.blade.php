<div class="p-4">
    <div class="flex justify-between mb-4">
        <h2 class="text-xl font-bold">Productos</h2>
        <button class="btn btn-primary" wire:click="create">Nuevo producto</button>
    </div>

    <table class="table w-full">
        <thead>
            <tr>
                <th>Imagen</th>
                <th>Nombre</th>
                <th>Precio</th>
                <th>Categoría</th>
                <th>Activo</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($products as $p)
                <tr>
                    <td>
                        @if($p->getFirstMediaUrl('products'))
                            <img src="{{ $p->getFirstMediaUrl('products', 'thumb') }}" class="w-16 h-16 object-cover" />
                        @endif
                    </td>
                    <td>{{ $p->name }}</td>
                    <td>${{ number_format($p->price, 0) }}</td>
                    <td>{{ $p->category->name ?? '-' }}</td>
                    <td>
                        <span class="badge {{ $p->active ? 'badge-success' : 'badge-error' }}">
                            {{ $p->active ? 'Sí' : 'No' }}
                        </span>
                    </td>
                    <td class="flex gap-2">
                        <button class="btn btn-sm btn-info" wire:click="edit({{ $p->id }})">Editar</button>
                        <button class="btn btn-sm btn-error" wire:click="delete({{ $p->id }})">Eliminar</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <x-modal wire:model="showModal">
        <x-slot name="title">{{ $editing ? 'Editar Producto' : 'Nuevo Producto' }}</x-slot>
        <x-slot name="content">
            <div class="space-y-4">
                <input wire:model.defer="form.name" class="input input-bordered w-full" placeholder="Nombre" />
                <textarea wire:model.defer="form.description" class="textarea textarea-bordered w-full" placeholder="Descripción"></textarea>
                <input type="number" wire:model.defer="form.price" class="input input-bordered w-full" placeholder="Precio" />
                <input wire:model.defer="form.unit" class="input input-bordered w-full" placeholder="Unidad (ej: kg, ml...)" />

                <select wire:model.defer="form.category_id" class="select select-bordered w-full">
                    <option value="">Seleccione categoría</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>

                <input type="file" wire:model="form.image" class="file-input file-input-bordered w-full" />

                <label class="flex items-center gap-2">
                    <input type="checkbox" wire:model.defer="form.active" class="checkbox" />
                    Activo
                </label>
            </div>
        </x-slot>
        <x-slot name="footer">
            <button class="btn btn-primary" wire:click="save">Guardar</button>
        </x-slot>
    </x-modal>
</div>
