<?php

namespace App\Http\Controllers;

use App\Enums\RoomRegistrationStatus;
use App\Http\Requests\RenewContractRequest;
use App\Http\Requests\StoreContractRequest;
use App\Http\Requests\TerminateContractRequest;
use App\Http\Requests\TransferContractRequest;
use App\Models\Bed;
use App\Models\Contract;
use App\Models\RoomRegistration;
use App\Services\ContractService;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ContractController extends Controller
{
    public function __construct(private readonly ContractService $service) {}

    public function index(Request $request): JsonResponse|View
    {
        $contracts = Contract::query()
            ->with([
                'student',
                'roomRegistration',
                'currentAllocation.bed.room.building',
            ])
            ->latest('signed_at')
            ->get();

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Lấy danh sách hợp đồng thành công.',
                'data' => $contracts,
            ]);
        }

        return view('contracts.index', compact('contracts'));
    }

    public function create(): View
    {
        $registrations = $this->eligibleRegistrationQuery()->get();

        return view('contracts.create', compact('registrations'));
    }

    public function eligibleRegistrations(): JsonResponse
    {
        $registrations = $this->eligibleRegistrationQuery()->get();

        return response()->json([
            'message' => 'Lấy danh sách đơn đủ điều kiện lập hợp đồng thành công.',
            'data' => $registrations,
        ]);
    }

    public function store(StoreContractRequest $request): JsonResponse|RedirectResponse
    {
        $contract = $this->service->create(
            $request->validated(),
            $request->user()?->id,
        );

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Tạo hợp đồng và phân giường thành công.',
                'data' => $contract,
            ], 201);
        }

        return redirect()
            ->route('contracts.show', $contract)
            ->with('success', 'Tạo hợp đồng và phân giường thành công.');
    }

    public function show(Request $request, Contract $contract): JsonResponse|View
    {
        $contract->load([
            'student',
            'roomRegistration.room.building',
            'creator',
            'currentAllocation.bed.room.building',
            'allocations.bed.room.building',
            'allocations.allocator',
            'allocations.releaser',
            'renewals.renewer',
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Lấy thông tin hợp đồng thành công.',
                'data' => $contract,
            ]);
        }

        $availableBeds = Bed::query()
            ->with('room.building')
            ->where('status', 'available')
            ->whereHas('room', fn ($query) => $query->where('status', '!=', 'maintenance'))
            ->orderBy('room_id')
            ->orderBy('bed_number')
            ->get();

        return view('contracts.show', compact('contract', 'availableBeds'));
    }

    public function transfer(
        TransferContractRequest $request,
        Contract $contract,
    ): JsonResponse|RedirectResponse {
        $contract = $this->service->transfer(
            $contract,
            $request->validated(),
            $request->user()?->id,
        );

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Chuyển phòng thành công.',
                'data' => $contract,
            ]);
        }

        return redirect()
            ->route('contracts.show', $contract)
            ->with('success', 'Chuyển phòng thành công.');
    }

    public function renew(
        RenewContractRequest $request,
        Contract $contract,
    ): JsonResponse|RedirectResponse {
        $contract = $this->service->renew(
            $contract,
            $request->validated(),
            $request->user()?->id,
        );

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Gia hạn hợp đồng thành công.',
                'data' => $contract,
            ], 201);
        }

        return redirect()
            ->route('contracts.show', $contract)
            ->with('success', 'Gia hạn hợp đồng thành công.');
    }

    public function terminate(
        TerminateContractRequest $request,
        Contract $contract,
    ): JsonResponse|RedirectResponse {
        $contract = $this->service->terminate(
            $contract,
            $request->validated(),
            $request->user()?->id,
        );

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Trả phòng và thanh lý hợp đồng thành công.',
                'data' => $contract,
            ]);
        }

        return redirect()
            ->route('contracts.show', $contract)
            ->with('success', 'Trả phòng và thanh lý hợp đồng thành công.');
    }

    private function eligibleRegistrationQuery(): Builder
    {
        return RoomRegistration::query()
            ->with([
                'student',
                'room.building',
                'room.beds' => fn ($query) => $query->where('status', 'available'),
            ])
            ->where('status', RoomRegistrationStatus::Approved->value)
            ->whereDoesntHave('contract')
            ->oldest('reviewed_at');
    }
}
