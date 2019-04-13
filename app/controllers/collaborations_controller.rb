class CollaborationsController < ApplicationController
  helper_method :payment_types

  # before_action :authenticate_user!
  before_action :set_collaboration, only: [:confirm, :confirm_bank, :edit, :modify, :destroy, :OK, :KO]

  def new
    redirect_to edit_collaboration_path and return if current_user.try(:collaboration)
    @collaboration = Collaboration.new
    # @collaboration.for_town_cc = true
    session.delete(:collaboration_id) if session[:collaboration_id]
  end

  def modify
    redirect_to new_collaboration_path and return unless @collaboration
    redirect_to confirm_collaboration_path and return unless @collaboration.has_payment?

    # update collaboration
    @collaboration.assign_attributes collaboration_params

    if @collaboration.save
      flash[:notice] = "Los cambios han sido guardados"
      redirect_to edit_collaboration_path
    else
      render 'edit'
    end
  end

  def create
    if current_user.nil?
      params[:non_user][:email] = params[:collaboration][:non_user_email]
      params[:non_user][:document_vatid] = params[:collaboration][:non_user_document_vatid]
      params[:non_user][:country] = "ES"
      params[:collaboration][:non_user_data] = Collaboration::NonUser.new(non_user_params).to_yaml
    end
    @collaboration = Collaboration.new(collaboration_params)
    @collaboration.user = current_user if not current_user.nil?
    @collaboration.territorial_assignment = "autonomy"

    respond_to do |format|
      if @collaboration.save
        session[:collaboration_id] = @collaboration.id if current_user.nil?
        format.html { redirect_to confirm_collaboration_url, notice: 'Por favor revisa y confirma tu colaboración.' }
        format.json { render :confirm, status: :created, location: confirm_collaboration_path }
      else
        format.html { render :new }
        format.json { render json: @collaboration.errors, status: :unprocessable_entity }
      end
    end
  end

  def edit
    redirect_to new_collaboration_path and return unless @collaboration
    redirect_to confirm_collaboration_path and return unless @collaboration.has_payment?
  end

  def destroy
    redirect_to new_collaboration_path and return unless @collaboration
    @collaboration.destroy
    respond_to do |format|
      format.html { redirect_to new_collaboration_path, notice: 'Hemos dado de baja tu colaboración.' }
      format.json { head :no_content }
    end
  end

  def confirm
    redirect_to new_collaboration_path and return unless @collaboration
    redirect_to edit_collaboration_path if @collaboration.has_payment?
    # ensure credit card order is not persisted, to allow create a new id for each payment try
    @order = @collaboration.create_order Time.now, true if @collaboration.is_credit_card?
  end

  def OK
    redirect_to new_collaboration_path and return unless @collaboration
    if not @collaboration.user.try(:email).nil? and @collaboration.mail_send_at.nil?
      CollaborationsMailer.successful_collaboration(@collaboration.user.email).deliver_now!
      @collaboration.update_attribute(:mail_send_at, Time.now)
    elsif not @collaboration.non_user_email.nil? and @collaboration.mail_send_at.nil?
      CollaborationsMailer.successful_collaboration(@collaboration.non_user_email).deliver_now!
      @collaboration.update_attribute(:mail_send_at, Time.now)
    end
    if not @collaboration.is_active?
      if @collaboration.is_credit_card?
        @collaboration.set_warning! "Marcada como alerta porque se ha visitado la página de que la colaboración está pagada pero no consta el pago."
      else
        @collaboration.set_active!
      end
    end
    session.delete(:collaboration_id) if session[:collaboration_id]
  end

  def KO
  end

  def payment_types_by_frequency
    @payment_types ||= begin
      ret = Order::PAYMENT_TYPES.to_a
      if params[:frequency] == "0"
        ret.reject! { |_, value| value != 1 }
      else
        ret.reject! { |_, value| value != 3 }
      end
    end
    render json: { payment_types: ret, status: true}
  end

  private

  def payment_types
    @payment_types ||= begin
      ret = Order::PAYMENT_TYPES.to_a
      # ret.reject! { |_, value| value == 1 } unless @collaboration.is_credit_card?
      ret.reject! { |_, value| value == 2 }
      ret
    end
  end

  # Use callbacks to share common setup or constraints between actions.
  def set_collaboration
    if current_user
      @collaboration = current_user.collaboration
    elsif session[:collaboration_id]
      @collaboration = Collaboration.find(session[:collaboration_id])
    end

    if @collaboration
      start_date = [@collaboration.created_at, Date.today - 6.months].max
      if @collaboration.frequency == 0
        @orders = @collaboration.get_orders(start_date, start_date + 12.months)[0..(12/12-1)]
        @order = @orders[0][-1]
      else
        @orders = @collaboration.get_orders(start_date, start_date + 12.months)[0..(12/@collaboration.frequency-1)]
        @order = @orders[0][-1]
      end
    end
  end

  # Never trust parameters from the scary internet, only allow the white list through.
  def collaboration_params
    params.require(:collaboration).permit(:amount, :frequency, :terms_of_service, :minimal_year_old, :payment_type, :ccc_entity, :ccc_office, :ccc_dc, :ccc_account, :iban_account, :iban_bic, :territorial_assignment, :non_user_email, :non_user_document_vatid, :non_user_data, :other_amount)
  end

  def non_user_params
    params.require(:non_user).permit(:legacy_id, :full_name, :document_vatid, :email, :phone, :address, :town_name, :postal_code, :country, :province, :province_name, :island_name, :autonomy_name, :ine_town)
  end
end
