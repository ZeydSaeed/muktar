export type FamilyTableRow = {
    id: number;
    head_of_family_name: string;
    profession: string | null;
    house_number: number | null;
    mobile_number: string | null;
    national_id_number: string | null;
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
    birth_year: string;
    males_op: string;
    males_count: string;
    females_op: string;
    females_count: string;
    ration_card_number: string;
    housing_card_number: string;
    nearest_landmark: string;
    karbala_op: string;
    karbala_date: string;
    karbala_date_to: string;
    current_op: string;
    current_date: string;
    current_date_to: string;
};

export type FamilyFilterOptions = {
    professions: FilterOption[];
    house_groups: FilterOption[];
    birth_years: FilterOption[];
    males_counts: FilterOption[];
    females_counts: FilterOption[];
    ration_card_numbers: FilterOption[];
    housing_card_numbers: FilterOption[];
    nearest_landmarks: FilterOption[];
    karbala_dates: FilterOption[];
    current_dates: FilterOption[];
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
