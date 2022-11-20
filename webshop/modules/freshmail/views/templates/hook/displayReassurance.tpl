<div class="card" id="fm_product_notify">
	<div class="card-body">
		<div class="col-12">
			<a class="btn btn-primary" data-toggle="collapse" href="#fm_collapse" role="button" aria-expanded="false" aria-controls="fm_collapse">
				{$prompt_text}
			</a>
		</div>
		<div class="col-12 mt-2 collapse" id="fm_collapse">
            <div id="fm_messages"></div>
			<form id="fm_watch_product">
				<div class="input-group mb-3">
					<input type="email" class="form-control" id="fm_email" placeholder="Adres e-mail" aria-label="Adres e-mail" aria-describedby="fmEmail">
                    <input type="hidden" id="fm_token" val="" />
					<div class="input-group-append">
						<button class="btn btn-outline-primary" type="submit" id="fmEmail">Zapisz</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>

<script>
const AJAX_INFO = "{$ajax_info}";
const AJAX_SAVE = "{$ajax_save}";

</script>
