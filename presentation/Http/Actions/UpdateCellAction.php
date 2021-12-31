<?php

namespace Presentation\Http\Actions;

use Application\Exceptions\NotFound;
use Application\Services\GameService;
use Domain\Enums\CellActions;
use Domain\Exceptions\InvalidParameters;
use Domain\Exceptions\InvalidStateMutation;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\Factory;
use Illuminate\Validation\Rule;
use Psr\Http\Message\ServerRequestInterface;

class UpdateCellAction
{
    public function __construct(private Factory $validatorFactory, private GameService $gameService)
    {
    }

    public function __invoke(ServerRequestInterface $request, string $id): JsonResponse
    {
        $body = $request->getParsedBody();
        $validator = $this->validatorFactory->make(
            $body,
            $this->getValidationRules(),
            ['required' => 'The :attribute field is required.']
        );
        if ($validator->fails()) {
            return Response::json(['error' => $validator->errors()->all(),], 400);
        }

        try {
            $game = $this->gameService->updateCell($id, $body['action'], $body['row'], $body['column']);

            return Response::json($game);
        } catch (InvalidStateMutation $e) {
            return Response::json(['error' => $e->getMessage(),], 409);
        } catch (InvalidParameters $e) {
            return Response::json(['error' => $e->getMessage(),], 400);
        } catch (NotFound $e) {
            return Response::json(['error' => 'Game not found',], 404);
        } catch (\Throwable $e) {
            Log::error($e->getMessage(), $e->getTrace());
            return Response::json(['error' => 'Internal Server Error',], 500);
        }
    }

    private function getValidationRules(): array
    {
        return [
            'action' => ['required', Rule::in(CellActions::values())],
            'row'    => 'required|integer',
            'column' => 'required|integer',
        ];
    }
}
