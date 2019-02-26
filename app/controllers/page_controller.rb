require 'securerandom'
class PageController < ApplicationController
  include ERB::Util

  before_action :authenticate_user!, except: [ :candidaturas, :privacy_policy, :faq, :guarantees, :funding, :guarantees_form, :show_form,
                                              :circles_validation, :primarias_andalucia, :listas_primarias_andaluzas,
                                              :responsables_organizacion_municipales, :count_votes,
                                              :responsables_municipales_andalucia, :plaza_podemos_municipal,
                                              :portal_transparencia_cc_estatal, :mujer_igualdad, :alta_consulta_ciudadana,
                                              :representantes_electorales_extranjeros, :responsables_areas_cc_autonomicos,
                                              :apoderados_campana_autonomica_andalucia, :comparte_cambio_valoracion_propietarios,
                                              :comparte_cambio_valoracion_usuarios, :avales_candidaturas_primarias, :iniciativa_ciudadana]

  before_filter :set_metas

  def set_metas
    @current_elections = Election.active
    election = @current_elections.select {|election| election.meta_description if !election.meta_description.blank? } .first


    @meta_description = Rails.application.secrets.metas["description"] if @meta_description.nil?
    @meta_image = Rails.application.secrets.metas["image"] if @meta_description.nil?
  end

  def show_form
    @page = Page.find(params[:id])

    @meta_description = @page.meta_description if !@page.meta_description.blank?
    @meta_image = @page.meta_image if !@page.meta_image.blank?
    if @page.require_login && !user_signed_in?
      flash[:metas] = { description: @meta_description, image: @meta_image }
      authenticate_user!
    end

    if /https:\/\/[^\/]*\.masmadrid.org\/.*/.match(@page.link)
      render :formview_iframe, locals: { title: @page.title, url: add_user_params(@page.link) }
    else
      render :form_iframe, locals: { title: @page.title, url: form_url(@page.id_form) }
    end
  end

  def privacy_policy
  end

  def candidaturas
  end

  def faq
  end

  def guarantees
  end

  def funding
  end


  private

  def form_url(id_form)
    sign_url(add_user_params("https://#{domain}/gfembed/?f=#{id_form}"))
  end

  def add_user_params(url)
    return url unless user_signed_in?

    params = {
      id: current_user.id,
      first_name: current_user.first_name,
      last_name: current_user.last_name,
      street: current_user.address,
      town: current_user.town_name,
      province: current_user.province_name,
      postal_code: current_user.postal_code,
      country: current_user.country,
      address: current_user.full_address,
      phone: current_user.phone,
      email: current_user.email,
      document_vatid: current_user.document_vatid,
      born_at: current_user.born_at.strftime('%d/%m/%Y'),
      autonomy: current_user.vote_autonomy_name,
      comunity: current_user.vote_autonomy_code,
      town_code: current_user.town,
      created_at: current_user.created_at,
      gender: current_user.gender,
      vote_town: current_user.vote_town,
      vote_autonomy_since: current_user.vote_autonomy_since.to_i,
      vote_province_since: current_user.vote_province_since.to_i,
      vote_island_since: current_user.vote_island_since.to_i,
      vote_town_since: current_user.vote_town_since.to_i
    }

    url + params.map { |param, value| "&participa_user_#{param}=#{u(value)}" } .join
  end

  def sign_url(url)
    timestamp = Time.now.to_i
    signature = Base64.urlsafe_encode64(OpenSSL::HMAC.digest("SHA256", secret, "#{timestamp}::#{url}")[0..20])
    "#{url}&signature=#{signature}&timestamp=#{timestamp}"
  end

  def domain
    @domain ||= Rails.application.secrets.forms["domain"]
  end

  def secret
    @secret ||= Rails.application.secrets.forms["secret"]
  end
end
