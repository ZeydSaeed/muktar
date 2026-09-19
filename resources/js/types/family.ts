export type FamilyTableRow = {
    id: number;
    head_of_family_name: string;
    profession: string | null;
    house_number: number | null;
    mobile_number: string | null;
    ration_card_number: number | null;
    birth_date: string | null;
    children_count: number;
};

export type FamilyChildRow = {
    id: number;
    name: string;
    birth_date: string | null;
    national_id_number: string | null;
    registry_number: number | null;
    page_number: number | null;
    profession: string | null;
};

export type ChildrenModal = {
    family_id: number;
    family_name: string;
    children: FamilyChildRow[];
};

export type PaginationLink = {
    url: string | null;
    label: string;
    active: boolean;
};

export type FilterOption = {
    value: string;
    label: string;
};

export type FamilyTableFilters = {
    profession: string;
    house_group: string;
    nearest_landmark: string;
    birth_year: string;
    ration_card_number: string;
    housing_card_number: string;
};

export type FamilyFilterOptions = {
    professions: FilterOption[];
    house_groups: FilterOption[];
    nearest_landmarks: FilterOption[];
    birth_years: FilterOption[];
    ration_card_numbers: FilterOption[];
    housing_card_numbers: FilterOption[];
};

export type PaginatedFamilies = {
    data: FamilyTableRow[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number | null;
    to: number | null;
    links: PaginationLink[];
};
