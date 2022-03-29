<?php
/**
 * RecurrenceController.php
 * Copyright (c) 2020 james@firefly-iii.org
 *
 * This file is part of Firefly III (https://github.com/firefly-iii).
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace FireflyIII\Api\V1\Controllers\Autocomplete;

use FireflyIII\Api\V1\Controllers\Controller;
use FireflyIII\Api\V1\Requests\Autocomplete\AutocompleteRequest;
use FireflyIII\Models\Recurrence;
use FireflyIII\Repositories\Recurring\RecurringRepositoryInterface;
use Illuminate\Http\JsonResponse;

/**
 * Class RecurrenceController
 */
class RecurrenceController extends Controller
{
    private RecurringRepositoryInterface $repository;

    /**
     * RecurrenceController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->middleware(
            function ($request, $next) {
                $this->repository = app(RecurringRepositoryInterface::class);
                $this->repository->setUser(auth()->user());

                return $next($request);
            }
        );
    }

    /**
     * This endpoint is documented at:
     * https://api-docs.firefly-iii.org/#/autocomplete/getRecurringAC
     *
     * @param AutocompleteRequest $request
     *
     * @return JsonResponse
     */
    public function recurring(AutocompleteRequest $request): JsonResponse
    {
        $data        = $request->getData();
        $recurrences = $this->repository->searchRecurrence($data['query'], $data['limit']);
        $response    = [];

        /** @var Recurrence $recurrence */
        foreach ($recurrences as $recurrence) {
            $response[] = [
                'id'          => (string) $recurrence->id,
                'name'        => $recurrence->title,
                'description' => $recurrence->description,
            ];
        }

        return response()->json($response);
    }

}
