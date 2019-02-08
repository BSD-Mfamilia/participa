module RegistrationsHelper
  require "ffi-icu"
  
  def self.region_comparer
    @collator ||= ICU::Collation::Collator.new("es_ES")
    @comparer ||= lambda {|a, b| @collator.compare(a.name, b.name)}
  end

  # lists of countries, current country provinces and current province towns, sorted with spanish collation
  def get_countries
    #Carmen::Country.all.sort &RegistrationsHelper.region_comparer
    sacar_solo_espania &RegistrationsHelper.region_comparer
  end

	def sacar_solo_espania
		Carmen::Country.all.select{|c| %w{ES}.include?(c.code)}
	end

	def sacar_solo_madrid
		c = Carmen::Country.coded('ES')
		c.subregions.select{|c| %w{M}.include?(c.code)}
	end

  def get_provinces country
    c = Carmen::Country.coded(country)
    
    if not (c and c.subregions)
      []
    else
      #c.subregions.sort &RegistrationsHelper.region_comparer
      sacar_solo_madrid &RegistrationsHelper.region_comparer
    end
  end

  def get_towns country, province
    p = if province && country =="ES" then 
          Carmen::Country.coded("ES").subregions.coded(province) 
        end

    if not (p and p.subregions)
      []
    else
      p.subregions.sort &RegistrationsHelper.region_comparer
    end
  end
end
