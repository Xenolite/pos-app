<div class="modal fade" id="createAccountModal">

    <div class="modal-dialog">

        <form action="{{ route('admin.accounts.store') }}"
              method="POST">

            @csrf

            <div class="modal-content">

                <div class="modal-header">
                    <h5>Create Account</h5>
                </div>

                <div class="modal-body">

                    <input type="text"
                           name="name"
                           class="form-control mb-3"
                           placeholder="Name">

                    <input type="email"
                           name="email"
                           class="form-control mb-3"
                           placeholder="Email">

                    <input type="text"
                           name="phone"
                           class="form-control mb-3"
                           placeholder="Phone">

                    <input type="password"
                           name="password"
                           class="form-control mb-3"
                           placeholder="Password">

                    <select name="role"
                            class="form-control">

                        <option value="cashier">
                            Cashier
                        </option>

                        <option value="admin">
                            Admin
                        </option>

                    </select>

                </div>

                <div class="modal-footer">

                    <button type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal">

                        Cancel

                    </button>

                    <button class="btn btn-warning">
                        Create
                    </button>

                </div>

            </div>

        </form>

    </div>

</div>