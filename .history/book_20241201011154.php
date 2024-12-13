<section class="content container">
  <br>
  <form method="post">
    <div class="container text-center">
      <div class="mb-3">
        <!-- Reservation Date -->
        <label><strong>Date of Delivery</strong></label>
        <input type="date" class="form-control w-50 mx-auto" name="date">
      </div>

      <div class="mb-3">
        <!-- Time of Delivery -->
        <label><strong>Time of Delivery</strong></label>
        <select name="time" class="form-control w-50 mx-auto">
          <option value="08:00 AM">08:00 AM</option>
          <option value="09:00 AM">09:00 AM</option>
          <option value="10:00 AM">10:00 AM</option>
          <option value="11:00 AM">11:00 AM</option>
          <option value="12:00 PM">12:00 PM</option>
          <option value="01:00 PM">01:00 PM</option>
          <option value="02:00 PM">02:00 PM</option>
          <option value="03:00 PM">03:00 PM</option>
          <option value="04:00 PM">04:00 PM</option>
          <option value="05:00 PM">05:00 PM</option>
          <option value="06:00 PM">06:00 PM</option>
          <option value="07:00 PM">07:00 PM</option>
          <option value="08:00 PM">08:00 PM</option>
          <option value="09:00 PM">09:00 PM</option>
          <option value="10:00 PM">10:00 PM</option>
          <option value="11:00 PM">11:00 PM</option>
        </select>
      </div>

      <div class="mb-3">
        <!-- Mode of Payment -->
        <label><strong>Mode of Payment</strong></label>
        <select name="mode_of_payment" class="form-control w-50 mx-auto" id="paymentMethod" required>
          <option value="Gcash">Gcash</option>
          <option value="Cash on Delivery">Cash on Delivery</option>
        </select>
      </div>

      <!-- Gcash Details Section -->
      <div id="gcashDetails" style="display: none;" class="mt-4">
        <div class="text-center mb-3">
          <img src="pics/GcashPayment.jpg" alt="Gcash QR Code" class="img-fluid" style="max-width: 200px;">
        </div>

        <div class="mb-3">
          <!-- Reference Number -->
          <label><strong>Reference Number</strong></label>
          <input type="text" class="form-control w-50 mx-auto" name="ref_no" placeholder="Enter Gcash reference number">
        </div>
      </div>

      <!-- Submit Button -->
      <div class="mt-4">
        <button class="btn btn-primary" name="next">Next</button>
        <button type="button" class="btn btn-secondary mx-1" onclick="window.location.href='home.php'">Back</button>
      </div>
    </div>
  </form>
</section>
